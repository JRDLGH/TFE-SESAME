<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Thesaurus\Gesture;
use App\Entity\Thesaurus\Gesture\Tag;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        /**
         * USER
         */
        // ADMIN
        $user = new User();
        $user->setFirstname('Jordan');
        $user->setLastname('LGH');
        $user->setEmail('admin@sesame.be');
        $user->setPassword('admin');
        $user->setUsername('admin');
        $user->setEnabled(true);
        $user->setRoles('ROLE_ADMIN');
        $manager->persist($user);
        //STANDARD USER
        $user = new User();
        $user->setFirstname('Jordan');
        $user->setLastname('lagache');
        $user->setEmail('user@sesame.be');
        $user->setPassword('user');
        $user->setUsername('user');
        $user->setEnabled(true);
        $manager->persist($user);
        /**
         * GESTURES
         */
        //Each gesture will be linked to one tag
        //Gesture1 linked to tag1, ...
        for ($i = 0; $i < 20; $i++) {
            //Creating tags
            $tag = new Tag();
            $tag->setKeyword('tag'.$i);
            $manager->persist($tag);
            //Creating gestures
            $gesture = new Gesture();
            //publish one on 3
            if($i % 3 == 0){
                $gesture->setIsPublished(true);
                $s = rand(1,5);
                for($j = 0; $j < $s; $j++){
                    $ntag = new Tag();
                    $ntag->setKeyword('tag'.$i.'v'.$j);
                    $manager->persist($ntag);
                    $gesture->addTag($ntag);
                }

            }else{
                $gesture->setIsPublished(false);
            }
            $gesture->setName('gesture'.$i);
            $gesture->setDescription('This is the description of gesture'.$i);
            $gesture->addTag($tag);
            //persist
            $manager->persist($gesture);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
