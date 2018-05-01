<?php

namespace App\Controller\Profiling;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfilingController
 * @package App\Controller\Profiling
 * @Route("/profiling")
 */
class ProfilingController extends Controller
{
    /**
     * @Route("/", name="profiling_home")
     */
    public function index()
    {
        return $this->render('profiling/profiling/index.html.twig', [
            'controller_name' => 'ProfilingController',
        ]);
    }
}
