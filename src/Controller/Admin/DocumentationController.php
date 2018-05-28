<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentationController extends Controller
{
    /**
     * @Route("/admin/documentation", name="admin_documentation")
     */
    public function index()
    {
        return $this->render('admin/documentation/index.html.twig', [
            'controller_name' => 'DocumentationController',
        ]);
    }
}
