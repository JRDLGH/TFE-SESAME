<?php

namespace App\Controller\Thesaurus\Gesture;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GestureController extends Controller
{
    /**
     * @Route("/thesaurus/gesture", name="thesaurus_gesture")
     */
    public function index()
    {
        return $this->render('thesaurus/gesture/index.html.twig', [
            'controller_name' => 'GestureController',
        ]);
    }
}
