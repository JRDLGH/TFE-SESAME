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
        $keywords = array_map('trim',explode(',',$string));

        //tolower all cells, mb because strtolower causes problem with UTF8
        $keywords = array_map('mb_strtolower',$keywords);
        //remove empty tags and duplicated ones
        $keywords = array_unique(array_filter($keywords));


        //Verify in database if they dont already exist
        $tags = $this->manager->getRepository(Tag::class)->findBy([
            'keyword' => $keywords
        ]);

        foreach($tags as $tag){
            //put each tag keyword as tolower
            $tag->setKeyword(mb_strtolower($tag->getKeyword()));
        }

        $newTags = array_diff($keywords,$tags);
        foreach ($newTags as $newTag){
            $tag = new Tag();
            $tag->setKeyword($newTag);
            $tags[] = $tag;
        }

        return $tags;
    }
}