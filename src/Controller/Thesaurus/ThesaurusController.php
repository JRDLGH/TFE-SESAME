<?php

namespace App\Controller\Thesaurus;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/search/{tag}",name="thesaurus_search_tag",options={"expose"=true})
     */
    public function searchByTag(TranslatorInterface $translator, $tag)
    {
        //If request from AJAX
        //Temporary disabling this if in order to test with postman
//        if($request->isXMLHttpRequest()){
            $tag_name = $tag;

            //if tag exist then search, either, search for gesture that match the name

            $gestures = $this->getDoctrine()->getRepository(Gesture::class)->findByTagName($tag_name);

            $status = ["status"=>["message"=> $translator->trans('status.gesture.error') ]];

            $formatted = [$status];

            if($gestures){
                var_dump($gestures);
                die();
            }else{
                //empty -- no response
                var_dump($gestures);
                $status = ["status"=>["message"=> $translator->trans('status.gesture.not_found') ]];
            }

            return new JsonResponse($formatted);
//        }
//        return new JsonResponse('Error: This request is not valid.',400);
    }
}
