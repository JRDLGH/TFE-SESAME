<?php

namespace App\Controller\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Repository\Profiling\ProfileRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/search",name="profiling_search_profile",methods={"GET"},options={"expose"=true})
     */
    public function search(Request $request)
    {
        //XHR REQUEST!
        if($request->isXmlHttpRequest())
        {

            $pattern = $request->get('pattern');
            $limit = $request->get('limit');

            if($pattern)
            {

                $pattern = array_filter(explode(' ',trim($pattern)));
                $pattern = implode(' ',$pattern);
                $disabledRepository = $this->getDoctrine()->getRepository(Disabled::class);
                if(empty($limit)){
                    $limit = 10;
                }
                $disableds = $disabledRepository->findAllBeginBy($pattern,intval($limit));

                if($disableds)
                {

                    return $this->json($disableds,200,[],['groups'=>['search']]);

                }

                return new JsonResponse(['not_found'=>'Aucun profil trouvé..'],404);

            }

        }
        else
        {
            return $this->render('profiling/profile/search.html.twig');
        }
        return new JsonResponse(['error'=>'Bad request sent.'],400);
    }
}
