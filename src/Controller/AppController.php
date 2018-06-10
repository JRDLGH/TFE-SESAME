<?php

namespace App\Controller;

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

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('app/about.html.twig',[
            'title' => 'À propos'
        ]);
    }
}
