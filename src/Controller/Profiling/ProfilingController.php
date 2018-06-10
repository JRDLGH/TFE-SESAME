<?php

namespace App\Controller\Profiling;

use App\Entity\Profiling\Disabled;
use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ProfilingController
 * @package App\Controller\Profiling
 * @Route("/profiling")
 */
class ProfilingController extends Controller
{
//    /**
//     * @Route("/", name="profiling_home")
//     */
//    public function index()
//    {
//        return $this->render('profiling/profile/index.html.twig');
//    }

    /**
     * @Route("/profile/{id}", name="profile_consult",requirements={"id"="\d+"}, methods={"GET"},options={"expose"=true})
     */
    public function consult($id)
    {

        $profileRepo = $this->getDoctrine()->getRepository(Profile::class);
        $profile = $profileRepo->find($id);
        return $this->render('profiling/profile/index.html.twig',
            [
                'profile' => $profile
            ]);
    }

    /**
     * @Route("/profile/gestures",name="profile_gestures",methods={"GET"},options={"expose"=true})
     */
    public function getProfileGestures(Request $request){
        //we must check that the person is authorized to consult the profile specified.
        if($request->isXmlHttpRequest()){
            if($request->query->getInt('id')){
                $id = $request->query->getInt('id');

                $profileGestureRepo = $this->getDoctrine()->getRepository(ProfileGesture::class);
                $gestures = $profileGestureRepo->findGesturesByProfileId($id);

                if($gestures){
                    return $this->json($gestures,200,[],['groups'=>array('list')]);
                }
            }
        }
        return new JsonResponse('Invalid request',Response::HTTP_BAD_REQUEST);

    }

    /**
     * @Route("/search/last",name="search_last_learned_gestures",methods={"GET"},options={"expose"=true})
     */
    public function getLastLearnedGesture(Request $request)
    {
//        $pId = $request->query->getInt('pid');
//        $pId = $request->query->getInt('pid');

        $lasts = $this->getDoctrine()->getManager()->getRepository(ProfileGesture::class)->findByLearningDate(8);

        dump($lasts);
        die();

        return new JsonResponse($lasts);
    }

    /**
     * @Route("/search/gesture",name="search_profile_gesture",methods={"GET"},options={"expose"=true})
     */
    public function searchProfileGesture(Request $request)
    {
        //If request from AJAX
        if($request->isXMLHttpRequest()){

            $tag = $request->get('tag');
            $profileId = $request->query->getInt('profile');

            $encoder = new JsonEncoder();
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

            $normalizer = new GetSetMethodNormalizer($classMetadataFactory);

            //Avoid infinite loop caused by ManyToMany relationship between them
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));

            // GET ALL GESTURES THAT MATCH TAG BEGINING BY $tag AND GESTURE NAME IS NOT BEGINNING BY $tag
            $gesturesTagMatched = $this->getDoctrine()->getRepository(ProfileGesture::class)
                ->findByProfile($tag,$profileId);
            $json = $serializer->serialize($gesturesTagMatched, 'json',array('groups' => array('list')));
            $gesturesTagMatched = json_decode($json);

            //GET ALL GESTURES WHERE NAME BEGIN BY $tag
            $gesturesNameMatched = $this->getDoctrine()->getRepository(ProfileGesture::class)
                ->findByNameBeginBy($tag,$profileId);
            $json = $serializer->serialize($gesturesNameMatched, 'json',array('groups' => array('list')));
            $gesturesNameMatched = json_decode($json);

            $response = ['matched' => ['byName' => [], 'byTag' => []], 'status' => []];

            if(!empty($gesturesNameMatched))
            {
                $response['matched']['byName'] = $gesturesNameMatched;
                if(!empty($gesturesTagMatched)){
                    foreach ($gesturesTagMatched as $profileGesture){
                        $gesture = $profileGesture->gesture;
                        $found = false;
                        foreach($gesturesNameMatched as $namedMatched)
                        {
                            $namedMatched = $namedMatched->gesture;
                            if($gesture->id === $namedMatched->id){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            array_push($response['matched']['byTag'],$gesture);
                        }
                    }
                }
                $status = ['success' => count($response['matched']['byTag']).' gesture(s) found by tag, '
                    .count($response['matched']['byName']).' by name'];
            }else if(!empty($gesturesTagMatched)){
                $response['matched']['byTag'] = $gesturesTagMatched;
                $status = ['success' => count($response['matched']['byTag']).' gestures found'];
            }else{
                $response = ['not_found' => 'Aucun geste ne correspond à votre recherche.'];
                return new JsonResponse($response,Response::HTTP_NOT_FOUND);
            }
            $response['status'] = $status;
            return new JsonResponse($response,Response::HTTP_OK);
        }
        return new JsonResponse('Error: This request is not valid.',Response::HTTP_BAD_REQUEST);
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
