<?php

namespace App\Controller\Thesaurus;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\Thesaurus\Gesture\Tag;
use App\Entity\Thesaurus\Gesture;
/**
 * @Route("/thesaurus")
 * 
 * @author Jordan Lagache <jordan.lgh.pro@gmail.com>
 */
class ThesaurusController extends AbstractController
{
    /**
     * @Route("/",name="thesaurus_index")
     * 
     */
    public function index()
    {
        //load gestures
        $gestures = $this->getDoctrine()->getRepository(Gesture::class)->findAll();

        return $this->render('thesaurus/index.html.twig', [
            'controller_name' => 'ThesaurusController',
            'gestures' => $gestures
        ]);
    }

    /**
     * @Route("/search",name="thesaurus_search_tag",options={"expose"=true})
     */
    public function searchByTagName(TranslatorInterface $translator, Request $request)
    {
        //If request from AJAX
        //Temporary disabling this if in order to test with postman
//        if($request->isXMLHttpRequest()){

            $tag = $request->get('tag');

            //second
            $gesturesTagMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByTagName($tag);

            //first
            $gesturesNameMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByName($tag);

            $response = [];
            $status = ['status' => ['error' => 'this status should never be reached']];

            if(!empty($gesturesNameMatched))
            {
                array_unshift($response,$gesturesNameMatched);
                if(!empty($gesturesTagMatched)){
                    foreach ($gesturesTagMatched as $gesture){
                        $found = false;
                        foreach($gesturesNameMatched as $namedMatched)
                        {
                            if($gesture['id'] === $namedMatched['id']){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            array_push($response,$gesture);
                        }
                    }
                }
                $status = ['status' => ['success' => count($response).' gestures found']];
            }else if(!empty($gesturesTagMatched)){
                $response = $gesturesTagMatched;
                $status = ['status' => ['success' => count($response).' gestures found']];
            }else{
                $response = ['status' => 'No gesture found.'];
                //No gesture found
            }
            array_unshift($status,$response);
            return new JsonResponse($response);
//        }
//        return new JsonResponse('Error: This request is not valid.',400);
    }
}
