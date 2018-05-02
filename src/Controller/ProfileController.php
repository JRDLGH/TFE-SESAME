<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="profile")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    /**
     * @Route("/{id}" name="profile_consult",requirements={"id"="\d+"})
     */
    public function consult()
    {
        //TODO
    }
}
