<?php

namespace App\Controller\Thesaurus;

use App\Helper\File\FileHelper;
use App\Helper\Thesaurus\ThesaurusHelper;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\Thesaurus\Gesture;

//SERIALIZER
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
// For annotations
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
/**
 * @Route("/thesaurus")
 * 
 * @author Jordan Lagache <jordan.lgh.pro@gmail.com>
 */
class ThesaurusController extends AbstractController
{
    /**
     * @Route("/",name="thesaurus_index")
     * 
     */
    public function index()
    {
        return $this->render('thesaurus/index.html.twig');
    }

    /**
     * @Route("/gestures/{id}",name="thesaurus_gesture_show_details",options={"expose"=true},requirements={
    "id"="\d+"
     *     })
     * @Method("GET")
     */
    public function gestureShow($id, Request $request){

        if($request->isXmlHttpRequest())
        {
            if($id)
            {
                $encoder = new JsonEncoder();
                $fileHelper = new FileHelper();
                //Extract group view from gesture class
                $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
                //GetSetMethodNormalizer is faster that ObjectNormalizer
                $normalizer = new ObjectNormalizer($classMetadataFactory);
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer(array($normalizer),array($encoder));
                //Request database
                $gestureIdMatched = $this->getDoctrine()->getRepository(Gesture::class)->findPublishedById($id);

                $fileHelper->setGestureVideoPath($gestureIdMatched);
                $fileHelper->setGestureProfileVideoPath($gestureIdMatched);
                //Serialize
                $matched = $serializer->serialize($gestureIdMatched,'json',array('groups' => array('show')));

                if(!empty($matched)){
                    return new JsonResponse($matched,Response::HTTP_OK);
                }else{
                    return new JsonResponse(Response::HTTP_NOT_FOUND);
                }
            }
            else
            {
                return new JsonResponse('Fatal: Parameter id is missing.',
                    Response::HTTP_BAD_REQUEST);
            }


        }
        return new JsonResponse('Error: This request is not valid.',
            Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/search",name="thesaurus_search_tag",options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function search(Request $request)
    {
        //If request from AJAX
        if($request->isXMLHttpRequest()){

            $tag = $request->get('tag');

            $encoder = new JsonEncoder();
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

            $normalizer = new GetSetMethodNormalizer($classMetadataFactory);

            //Avoid infinite loop caused by ManyToMany relationship between them
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $fileHelper = new FileHelper();


            $serializer = new Serializer(array($normalizer), array($encoder));

            // GET ALL GESTURES THAT MATCH TAG BEGINING BY $tag AND GESTURE NAME IS NOT BEGINNING BY $tag
            $gesturesTagMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByTagNameExcludeNameBeginBy($tag);

            foreach($gesturesTagMatched as $gesture){
                if( empty($fileHelper->getFilePath($gesture->getVideoFile())) && empty($fileHelper->getFilePath($gesture->getVideoFile())) ){
                    $gesture->setHasVideos(false);
                }else{
                    $gesture->setHasVideos(true);
                }
            }


            $fileHelper->setGesturesCoverPath($gesturesTagMatched);

            $json = $serializer->serialize($gesturesTagMatched, 'json',array('groups' => array('list')));
            $gesturesTagMatched = json_decode($json);


            //GET ALL GESTURES WHERE NAME BEGIN BY $tag
            $gesturesNameMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByNameBeginBy($tag);

            foreach($gesturesNameMatched as $gesture){
                if( empty($fileHelper->getFilePath($gesture->getVideoFile())) && empty($fileHelper->getFilePath($gesture->getVideoFile())) ){
                    $gesture->setHasVideos(false);
                }else{
                    $gesture->setHasVideos(true);
                }
            }

            $fileHelper->setGesturesCoverPath($gesturesNameMatched);

            $json = $serializer->serialize($gesturesNameMatched, 'json',array('groups' => array('list')));
            $gesturesNameMatched = json_decode($json);

            $response = ['matched' => ['byName' => [], 'byTag' => []], 'status' => []];
            if(!empty($gesturesNameMatched))
            {
                $response['matched']['byName'] = $gesturesNameMatched;
                if(!empty($gesturesTagMatched)){
                    foreach ($gesturesTagMatched as $gesture){
                        $found = false;
                        foreach($gesturesNameMatched as $namedMatched)
                        {
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
}
