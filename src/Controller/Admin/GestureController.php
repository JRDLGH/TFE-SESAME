<?php

namespace App\Controller\Admin;

use App\Entity\Thesaurus\Gesture;
use App\Form\Thesaurus\GestureType;
use App\Repository\GestureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/thesaurus/gesture")
 */
class GestureController extends Controller
{
    /**
     * @Route("/", name="thesaurus_gesture_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {

//        $gestures = $gestureRepository->findAll();
        $paginator = $this->get('knp_paginator');
        $dql = 'SELECT g FROM App\Entity\Thesaurus\Gesture g';
        $manager = $this->getDoctrine()->getManager();
        $queryBuilder = $manager->getRepository(Gesture::class)->createQueryBuilder('g');

        if($request->query->get('filter')){
            if(!empty($request->query->get('filter'))){
                $queryBuilder->where('g.name LIKE :name')
                    ->setParameter('name','%'. $request->query->get('filter') .'%');
            }

        }

        $query = $queryBuilder->getQuery();

        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)
        );

        if($request->isXmlHttpRequest()){
            return $this->render('thesaurus/gesture/list_pagination.html.twig', [
                'gestures' => $result]);
        }
        return $this->render('thesaurus/gesture/index.html.twig', [
            'gestures' => $result
        ]);
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
            if($form->getData()->getIsPublished() && empty($form->getData()->getPublicationDate()))
            {
                $form->getData()->setPublicationDate(new \DateTime("now",new \DateTimeZone("Europe/Brussels")));
            }
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
     * @Route("/tags",name="tags_json",options={"expose"="true"})
     *
     */
    public function getTags(){
        $tags = $this->getDoctrine()->getRepository(Gesture\Tag::class)->findAll();
        return $this->json($tags,200,[],['groups'=>['list']]);
//        return new JsonResponse('hello');
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