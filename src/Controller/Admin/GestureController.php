<?php

namespace App\Controller\Admin;

use App\Entity\Thesaurus\Gesture;
use App\Form\Thesaurus\GestureType;
use App\Repository\GestureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/thesaurus/gesture")
 */
class GestureController extends Controller
{
    /**
     * @Route("/", name="thesaurus_gesture_index", methods="GET")
     */
    public function index(GestureRepository $gestureRepository): Response
    {
        return $this->render('thesaurus/gesture/index.html.twig', ['gestures' => $gestureRepository->findAll()]);
    }

    /**
     * @Route("/new", name="thesaurus_gesture_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $gesture = new Gesture();
        $form = $this->createForm(GestureType::class, $gesture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gesture);
            $em->flush();

            return $this->redirectToRoute('thesaurus_gesture_index');
        }

        return $this->render('thesaurus/gesture/new.html.twig', [
            'gesture' => $gesture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="thesaurus_gesture_show", methods="GET")
     */
    public function show(Gesture $gesture): Response
    {
        return $this->render('thesaurus/gesture/show.html.twig', ['gesture' => $gesture]);
    }

    /**
     * @Route("/{id}/edit", name="thesaurus_gesture_edit", methods="GET|POST")
     */
    public function edit(Request $request, Gesture $gesture): Response
    {
        $form = $this->createForm(GestureType::class, $gesture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($form->getData()->getIsPublished());
            //if published and previous published data is empty, set published data to today!
            if($form->getData()->getIsPublished() && empty($form->getData()->getPublicationDate()))
            {
                $form->getData()->setPublicationDate(new \DateTime("now",new \DateTimeZone("Europe/Brussels")));
            }
            else if(!$form->getData()->getIsPublished() && !empty($form->getData()->getPublicationDate()))
            {
                $form->getData()->setPublicationDate(null);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Vos modifications ont été enregistrées.');
            return $this->redirectToRoute('thesaurus_gesture_show', ['id' => $gesture->getId()]);
        }

        return $this->render('thesaurus/gesture/edit.html.twig', [
            'gesture' => $gesture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="thesaurus_gesture_delete", methods="DELETE")
     */
    public function delete(Request $request, Gesture $gesture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gesture->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gesture);
            $em->flush();
        }

        return $this->redirectToRoute('thesaurus_gesture_index');
    }
}
