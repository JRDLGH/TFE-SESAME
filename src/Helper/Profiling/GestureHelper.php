<?php

namespace App\Helper\Profiling;

use App\Entity\Profiling\Profile;
use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Persistence\ObjectManager;

class GestureHelper{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function isPublished(Gesture $gesture) : bool
    {
        $published = false;

        if($gesture->getId()){
            $storedGesture = $this->manager->getRepository(Gesture::class)->find($gesture->getId());
            if($storedGesture->getIsPublished()){
                $published = true;
            }
        }
        return $published;
    }

    public function isExisting(Gesture $gesture) : bool
    {
        $exists = false;

        if($gesture->getId()){

            $storedGesture = $this->manager->getRepository(Gesture::class)->find($gesture->getId());

            if($storedGesture)
            {
                if($storedGesture->getId() === $gesture->getId())
                {
                    $exists = true;
                }
            }

        }

        return $exists;
    }
}