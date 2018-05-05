<?php

namespace App\Controller\Management\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Entity\Thesaurus\Gesture;
use App\Form\Profiling\ProfileType;
use App\Helper\Profiling\ProfileHelper;
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
     * @Route("/add/gesture", name="management_profile_gesture", methods="GET|POST", options={"expose"=true})
     */
    public function gesturesToProfiles(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $profiles = $request->get('profiles');
            $gestures = $request->get('gestures');
            if(!empty($profiles) && !empty($gestures)){
                $manager = $this->getDoctrine()->getManager();
                $profileHelper = new ProfileHelper($manager);

                $not_found = false;
                foreach ($profiles as $id){
                    $ids[] = $id;
                    $profile = new Profile();
                    $profile->setId(intval($id));
                    if(!$profileHelper->isExisting($profile)){
                        $not_found = true;
                        break;
                    }
                }
                if($not_found){
                    return new JsonResponse(['Error'=>'Les profils que vous essayez d\'enrichir n\'existent pas'],404);
                }
                //verify that is valid profile
                //verify that is this profile can be accessed by this user
                //verify if gesture exist and is published.

                return new JsonResponse(['success'=>'1']);

            }
            return new JsonResponse(['Error'=>'Bad request.'],500);
        }

        //if data is submitted, verify it!

        return $this->render('management/profiling/profile/gesture_to_profile.html.twig');
    }

    /**
     * @Route("/search/gesture",name="profiling_search_gesture",methods={"GET"},options={"expose"=true})
     */
    public function searchGesture(Request $request){
        //Only XHTTP REQUEST
        if($request->isXmlHttpRequest()){
            $name = $request->get('name');
            if($name){
                $gestureRepository = $this->getDoctrine()->getRepository(Gesture::class);
                $gestures = $gestureRepository->findByNameBeginBy($name,10);
                if($gestures){
                    return $this->json($gestures,200,[],['groups'=>['minimal']]);
                }

                return new JsonResponse(['not_found'=>'Aucun geste trouvé.'],404);

            }
        }
        return new JsonResponse(['error'=>'Bad request sent, parameter is missing.'],400);
    }

    /**
     * @Route("/search",name="profiling_search_profile",methods={"GET"},options={"expose"=true})
     */
    public function search(Request $request){
        //XHR REQUEST!
        if($request->isXmlHttpRequest()){
            $pattern = $request->get('pattern');
            if($pattern){
                $pattern = array_filter(explode(' ',trim($pattern)));
                $pattern = implode(' ',$pattern);
                $disabledRepository = $this->getDoctrine()->getRepository(Disabled::class);
                $disableds = $disabledRepository->findAllBeginBy($pattern,10);
                if($disableds){
                    return $this->json($disableds,200,[],['groups'=>['search']]);
                }
                return new JsonResponse(['not_found'=>'Aucun profil trouvé..'],404);
            }
        }
        return new JsonResponse(['error'=>'Bad request sent, parameter is missing.'],400);
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
