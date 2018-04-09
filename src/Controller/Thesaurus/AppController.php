<?php

namespace App\Controller\Thesaurus;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Route("/home", name="app_home")
     */
    public function index()
    {
        return $this->render('app/index.html.twig', [
            'title' => 'Accueil',
        ]);
    }
}
