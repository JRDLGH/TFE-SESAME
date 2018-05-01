<?php

namespace App\Controller\Management\Profiling;

use App\Entity\Profiling\Profile;
use App\Form\Profiling\ProfileType;
use App\Repository\Profiling\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("manage/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="manage_profile_index", methods="GET")
     */
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('profiling/profile/index.html.twig', ['profiles' => $profileRepository->findAll()]);
    }

    /**
     * @Route("/new", name="manage_profile_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();

            return $this->redirectToRoute('profiling_profile_index');
        }

        return $this->render('profiling/profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manage_profile_show", methods="GET")
     */
    public function show(Profile $profile): Response
    {
        return $this->render('profiling_profile/show.html.twig', ['profile' => $profile]);
    }

    /**
     * @Route("/{id}/edit", name="manage_profile_edit", methods="GET|POST")
     */
    public function edit(Request $request, Profile $profile): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profiling_profile_edit', ['id' => $profile->getId()]);
        }

        return $this->render('profiling/profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manage_profile_delete", methods="DELETE")
     */
    public function delete(Request $request, Profile $profile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile);
            $em->flush();
        }

        return $this->redirectToRoute('profiling/profile_index');
    }
}
