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

            $gesturesTagMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByTagName($tag);

            $gesturesNameMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByName($tag);

            $response = ['matched' => ['byName' => [], 'byTag' => []], 'status' => []];

            if(!empty($gesturesNameMatched))
            {
                $response['matched']['byName'] = $gesturesNameMatched;
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
                            array_push($response['matched']['byTag'],$gesture);
                        }
                    }
                }
                $status = ['success' => count($response['matched']['byTag']).' gesture(s) found by tag, '
                    .count($response['matched']['byName']).' by name'];
            }else if(!empty($gesturesTagMatched)){
                $response['matched']['byTag'] = $gesturesTagMatched;
                $status = ['success' => count($response['matched']['byTag']).' gestures found'];
            }else{
                $response = ['not_found' => 'No gesture found.'];
                return new JsonResponse($response,Response::HTTP_NOT_FOUND);
            }
            $response['status'] = $status;
            return new JsonResponse($response,Response::HTTP_OK);
//        }
//        return new JsonResponse('Error: This request is not valid.',Response::HTTP_BAD_REQUEST);
    }
}
