<?php

namespace App\Controller\Profiling;

use App\Entity\Profiling\Profile;
use App\Repository\Profiling\ProfileRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfilingController
 * @package App\Controller\Profiling
 * @Route("/profiling")
 */
class ProfilingController extends Controller
{
    /**
     * @Route("/", name="profiling_home")
     */
    public function index()
    {
        return $this->render('profiling/profile/index.html.twig');
    }

    /**
     * @Route("/profile/{id}", name="profile_consult",requirements={"id"="\d+"}, methods={"GET"})
     */
    public function consult($id)
    {

        $profileRepo = $this->getDoctrine()->getRepository(Profile::class);
        $profile = $profileRepo->find($id);
//        dump($profile->getOwner()->getDeficiencies());
//        dump($profile->getLearnedGestures()->getValues());
//        die();
        return $this->render('profiling/profile/index.html.twig',
            [
                'profile' => $profile
            ]);
    }
}
