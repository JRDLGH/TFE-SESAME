<?php

namespace App\Controller\Admin;

use http\Env\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentationController extends Controller
{
    /**
     * @Route("/admin/docs", name="admin_documentation")
     */
    public function index()
    {
        return $this->render('admin/documentation/index.html.twig');
    }

    /**
     * @Route("/admin/docs/{section}",name="admin_documentation_get",options={"expose"=true},methods={"GET"})
     *
     */
    public function getDocSection(Request $request, $section)
    {
        if($request->isXmlHttpRequest())
        {
            if($section){
                $exists = $this->get('templating')->exists('admin/documentation/structure/content/'.$section.'.html.twig');
                if($exists){
                    return $this->render('admin/documentation/structure/content/'.$section.'.html.twig');
                }
                return new JsonResponse("Error: documentation not found for $section",404,[],true);
            }
        }
        return new JsonResponse("Error: Bad request",400,[],true);

    }
}
