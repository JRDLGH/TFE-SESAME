<?php

namespace App\Controller\Thesaurus;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/gesture/list",name="thesaurus_gesture_list",options={"expose"="true"})
     */
    public function getPublishedGesture(Request $request){
        if($request->isXMLHttpRequest()){
            $gestures = $this->getDoctrine()->getRepository(Gesture::class)->findAllPublished();
//            //need to do a lighter version of it! only return important things
//
            $encoder = array(new JsonEncoder());
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object){
                return $object->getName();
            });

            $serializer = new Serializer(array($normalizer), $encoder);
            $formatted = $serializer->serialize($gestures, 'json');
//            $formatted = 'hello';

            return new JsonResponse($formatted);
        }
        return new JsonResponse('Error: This request is not valid.',400);
    }
    /**
     * @Route("/test",name="thesaurus_test",options={"expose"=true})
     */
    public function test(Request $request)
    {
        //If request from AJAX
        if($request->isXMLHttpRequest()){
            $tag_id = $request->get('id');

            $gestures = $this->getDoctrine()->getRepository(Gesture::class)->find(50); #use serializer?

            $encoder = array(new JsonEncoder());
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getName();
            });

            $serializer = new Serializer(array($normalizer), $encoder);
            $formatted = $serializer->serialize($gestures, 'json');

            return new JsonResponse($formatted);
        }
        return new JsonResponse('Error: This request is not valid.',400);
    }
}
