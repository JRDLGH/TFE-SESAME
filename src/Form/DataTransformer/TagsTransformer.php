<?php

namespace App\Form\DataTransformer;

use App\Entity\Thesaurus\Gesture\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
    $this->manager = $manager;
    }

    public function transform($tags) : string
    {
        return implode(',',$tags);
    }
    public function reverseTransform($string) : array
    {
        //Get all keywords used
        $keywords = explode(',',$string);


        //Verify in database if they dont already exist
        $tags = $this->manager->getRepository(Tag::class)->findBy([
            'keyword' => $keywords
        ]);

        $newTags = array_diff($keywords,$tags);
    }

}