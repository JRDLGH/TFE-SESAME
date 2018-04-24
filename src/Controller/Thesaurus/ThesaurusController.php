<?php

namespace App\Controller\Thesaurus;

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
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\Thesaurus\Gesture\Tag;
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
        //load gestures
        $gestures = $this->getDoctrine()->getRepository(Gesture::class)->findAll();

        return $this->render('thesaurus/index.html.twig', [
            'controller_name' => 'ThesaurusController',
            'gestures' => $gestures
        ]);
    }

    /**
     * @Route("/gestures/{id}",name="thesaurus_gesture_show",options={"expose"=true},requirements={
        "id"="\d+"
*     })
     * @Method("GET")
     */
    public function gestureShow($id, Request $request){
//        if($request->isXmlHttpRequest()){
            if($id){

                $encoder = new JsonEncoder();

                //Extract group view from gesture class
                $classMetaDataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

                //GetSetMethodNormalizer is faster that ObjectNormalizer
                $normalizer = new GetSetMethodNormalizer($classMetaDataFactory);

                $serializer = new Serializer(array($normalizer),array($encoder));

                //Request database
                $gestureIdMatched = $this->getDoctrine()->getRepository(Gesture::class)->findPublishedById($id);

//                echo '<pre>'.print_r($gestureIdMatched,1).'</pre>';
                var_dump($gestureIdMatched);
                //Serialize
//                $json = $serializer->serialize($gestureIdMatched,'json',array('groups' => array('view')));


                return new JsonResponse($gestureIdMatched);

            }else{
                return new JsonResponse('Error: This request does not respect the requirements',Response::HTTP_BAD_REQUEST);
            }
//        }
        return new JsonResponse('Error: This request is not valid.',Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/search",name="thesaurus_search_tag",options={"expose"=true})
     * @Method("GET")
     */
    public function search(TranslatorInterface $translator, Request $request)
    {
        //If request from AJAX
        //Temporary disabling this if in order to test with postman
        if($request->isXMLHttpRequest()){

            $tag = $request->get('tag');

            $encoder = new JsonEncoder();
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

            $normalizer = new GetSetMethodNormalizer($classMetadataFactory);

            //Avoid infinite loop caused by ManyToMany relationship between them
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));

            // GET ALL GESTURES THAT MATCH TAG BEGINING BY $tag AND GESTURE NAME IS NOT BEGINNING BY $tag
            $gesturesTagMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByTagNameExcludeNameBeginBy($tag);
            $json = $serializer->serialize($gesturesTagMatched, 'json',array('groups' => array('list')));
            $gesturesTagMatched = json_decode($json);

            //GET ALL GESTURES WHERE NAME BEGIN BY $tag
            $gesturesNameMatched = $this->getDoctrine()->getRepository(Gesture::class)->findByNameBeginBy($tag);
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
                $response = ['not_found' => 'No gesture found.'];
                return new JsonResponse($response,Response::HTTP_NOT_FOUND);
            }
            $response['status'] = $status;
            return new JsonResponse($response,Response::HTTP_OK);
        }
        return new JsonResponse('Error: This request is not valid.',Response::HTTP_BAD_REQUEST);
    }
}
