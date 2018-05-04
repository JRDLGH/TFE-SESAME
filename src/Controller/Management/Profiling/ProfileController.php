<?php

namespace App\Controller\Management\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Entity\Thesaurus\Gesture;
use App\Form\Profiling\ProfileType;
use App\Repository\Profiling\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/add/gesture", name="management_profile_gesture", methods="GET|POST")
     */
    public function gesturesToProfiles(Request $request)
    {

        //if data is submitted, verify it!

        return $this->render('management/profiling/profile/gesture_to_profile.html.twig');
    }

    /**
     * @Route("/search/gesture",name="profiling_search_gesture",methods={"GET"},options={"expose"=true})
     */
    public function searchGesture(Request $request){
        //Only XHTTP REQUEST
        $name = $request->get('name');
        if($name){
            $gestureRepository = $this->getDoctrine()->getRepository(Gesture::class);
            $gestures = $gestureRepository->findByNameBeginBy($name);
            if($gestures){
                return $this->json($gestures,200,[],['groups'=>['minimal']]);
            }

            return new Response(['not_found'=>'Aucun geste trouvé.'],404);

        }
        return new Response(['error'=>'Bad request sent, parameter is missing.'],400);
    }

    /**
     * @Route("/search",name="",methods={"GET"},options={"expose"=true})
     */
    public function search(Request $request){
        //XHR REQUEST!
        $pattern = $request->get('pattern');
        if($pattern){
            $pattern = array_filter(explode(' ',trim($pattern)));
            $pattern = implode(' ',$pattern);
            $disabledRepository = $this->getDoctrine()->getRepository(Disabled::class);
            $disableds = $disabledRepository->findAllBeginBy($pattern);
        }
        //retrun disabled person with a link to his profile
        return new Response('hello',200);
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
