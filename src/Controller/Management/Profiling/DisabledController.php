<?php

namespace App\Controller\Management\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Form\Profiling\DisabledType;
use App\Repository\Profiling\DisabledRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("manage/disabled")
 */
class DisabledController extends Controller
{
    /**
     * @Route("/", name="management_disabled_index", methods="GET")
     */
    public function index(DisabledRepository $disabledRepository): Response
    {
        return $this->render('management/profiling/disabled/index.html.twig', ['disableds' => $disabledRepository->findAll()]);
    }

    /**
     * @Route("/new", name="management_disabled_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $disabled = new Disabled();
        $form = $this->createForm(DisabledType::class, $disabled);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disabled);
            $em->flush();

            return $this->redirectToRoute('management_disabled_index');
        }

        return $this->render('management/profiling/disabled/new.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="management_disabled_show", methods="GET")
     */
    public function show(Disabled $disabled): Response
    {
        return $this->render('management/profiling/disabled/show.html.twig', ['disabled' => $disabled]);
    }

    /**
     * @Route("/{id}/edit", name="management_disabled_edit", methods="GET|POST")
     */
    public function edit(Request $request, Disabled $disabled): Response
    {
        $form = $this->createForm(DisabledType::class, $disabled);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('management_disabled_edit', ['id' => $disabled->getId()]);
        }

        return $this->render('management/profiling/disabled/edit.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="management_disabled_delete", methods="DELETE")
     */
    public function delete(Request $request, Disabled $disabled): Response
    {
        if ($this->isCsrfTokenValid('delete'.$disabled->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disabled);
            $em->flush();
        }

        return $this->redirectToRoute('management_disabled_index');
    }
}
