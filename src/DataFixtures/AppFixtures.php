<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Thesaurus\Gesture;
use App\Entity\Thesaurus\Gesture\Tag;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        //Each gesture will be linked to one tag
        //Gesture1 linked to tag1, ...
        for ($i = 0; $i < 100; $i++) {
            //Creating tags
            $tag = new Tag();
            $tag->setKeyword('tag'.$i);
            $manager->persist($tag);
            //Creating gestures
            $gesture = new Gesture();
            $gesture->setName('gesture '.$i);
            $gesture->setDescription('This is the description of gesture number '.$i);
            $gesture->setIsPublished(false);

            //persist
            $gesture->addTag($tag);
            $manager->persist($gesture);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
