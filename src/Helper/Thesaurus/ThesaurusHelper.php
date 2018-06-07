<?php
/**
 * Created by PhpStorm.
 * User: JRDN
 * Date: 16/05/2018
 * Time: 08:29
 */

namespace App\Helper\Thesaurus;


use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ThesaurusHelper
{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function search($tag)
    {
        $encoder = new JsonEncoder();
        try {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        } catch (AnnotationException $e) {
        }

        $normalizer = new GetSetMethodNormalizer($classMetadataFactory);

        //Avoid infinite loop caused by ManyToMany relationship between them
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));

        // GET ALL GESTURES THAT MATCH TAG BEGINING BY $tag AND GESTURE NAME IS NOT BEGINNING BY $tag
        $gesturesTagMatched = $this->manager->getRepository(Gesture::class)->findByTagNameExcludeNameBeginBy($tag);
        $json = $serializer->serialize($gesturesTagMatched, 'json',array('groups' => array('list')));
        $gesturesTagMatched = json_decode($json);

        //GET ALL GESTURES WHERE NAME BEGIN BY $tag
        $gesturesNameMatched = $this->manager->getRepository(Gesture::class)->findByNameBeginBy($tag);
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
    }
}