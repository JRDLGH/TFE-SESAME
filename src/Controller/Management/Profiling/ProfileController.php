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
     * @Route("/", name="management_profile_index", methods="GET")
     */
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('management/profiling/profile/index.html.twig', ['profiles' => $profileRepository->findAll()]);
    }

    /**
     * @Route("/new", name="management_profile_new", methods="GET|POST")
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

            return $this->redirectToRoute('management_profile_index');
        }

        return $this->render('management/profiling/profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add/gesture", name="management_profile_gesture", methods={"GET"})
     */
    public function gesturesToProfiles()
    {
        return $this->render('management/profiling/profile/gesture_to_profile.html.twig');
    }

    /**
     * @Route("/{id}", name="management_profile_show", methods="GET")
     */
    public function show(Profile $profile): Response
    {
        return $this->render('management/profiling/profile/show.html.twig', ['profile' => $profile]);
    }

    /**
     * @Route("/{id}/edit", name="management_profile_edit", methods="GET|POST")
     */
    public function edit(Request $request, Profile $profile): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('management_profile_edit', ['id' => $profile->getId()]);
        }

        return $this->render('management/profiling/profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="management_profile_delete", methods="DELETE")
     */
    public function delete(Request $request, Profile $profile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile);
            $em->flush();
        }

        return $this->redirectToRoute('management_profile_index');
    }
}
