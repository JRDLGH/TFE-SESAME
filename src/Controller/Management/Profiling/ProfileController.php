<?php

namespace App\Controller\Management\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use App\Form\Profiling\ProfileType;
use App\Helper\Profiling\GestureHelper;
use App\Helper\Profiling\ProfileGestureHelper;
use App\Helper\Profiling\ProfileHelper;
use App\Repository\Profiling\ProfileRepository;
use ClassesWithParents\G;
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
     * @Route("/", name="management_profile_index", methods="GET", options={"expose"=true})
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

            $profilesIds = $request->get('profiles');
            $gesturesIds = $request->get('gestures');


            if(!empty($profilesIds) && !empty($gesturesIds))
            {

                $manager = $this->getDoctrine()->getManager();

                $profileHelper = new ProfileHelper($manager);

                //Collection of Profile
                $profiles = [];

                $not_found = false;

                foreach ($profilesIds as $id)
                {

                    $profile = new Profile();
                    $profile->setId(intval($id));

                    if(!$profileHelper->isExisting($profile)){

                        $not_found = true;
                        break;

                    }
                    $profiles[] = $profile;

                }

                if($not_found)
                {

                    return new JsonResponse(['Error'=>'Certains profils que vous essayez d\'enrichir n\'existent pas.'],404);

                }

                $gestureHelper = new GestureHelper($manager);

                //Collection of Profile
                $gestures = [];
                $gestureRepository = $manager->getRepository(Gesture::class);

                foreach ($gesturesIds as $id)
                {

                    $gesture = new Gesture();
                    $gesture->setId(intval($id));

                    //verify if gesture exist and is published.
                    if(!$gestureHelper->isExisting($gesture) || !$gestureHelper->isPublished($gesture)){
                        $not_found = true;
                        break;
                    }
                    //we need to get the gesture from db or doctrine wont be able to update/flush correctly!
                    $gestures[] = $gestureRepository->find($gesture->getId());
                }

                if($not_found)
                {
                    return new JsonResponse(['Error'=>'Certains des gestes ajoutés n\'existent pas.'],404);
                }

                $profileGestureHelper = new ProfileGestureHelper($manager);

//                If profiles exists, gestures exists and are published, then add them & FLUSH

                //verify that is this profile can be accessed by this user
                foreach ($profiles as $profile){
                    foreach ($gestures as $gesture){
//                        for a profile, persist each gesture if not already learned
                        $profile = $profileHelper->addLearnedGesture($profile,$gesture,$profileGestureHelper);

                    }
                }

                $manager->flush();


                return new JsonResponse(['success'=>'L\'action a été effectuée avec succès. Vous allez être redirigé dans quelques instants.']);

            }
            return new JsonResponse(['Error'=>'Veuillez indiquez au moins un profil ou un geste.'],500);
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
