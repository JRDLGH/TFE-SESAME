<?php

namespace App\Controller\Thesaurus;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/test",name="thesaurus_test")
     */
    public function test(Request $request)
    {
        //If request from AJAX
        if($request->isXMLHttpRequest()){
            $tag_id = $request->get('id');
            var_dump($tag_id);
            die();
        }
        return new JsonResponse('Error: This request is not valid.',400);
    }
}
