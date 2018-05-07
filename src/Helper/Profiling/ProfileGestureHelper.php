<?php

namespace App\Helper\Profiling;

use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Persistence\ObjectManager;

class ProfileGestureHelper{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Verify if a gesture can be learned by someone by verifying first that the gesture is existing, published and then
     * by verifying if the gesture is not already learned by the profile.
     *
     * @param Gesture $gesture, the learned gesture to verify
     * @param Profile $profile, the profile of the person who learned the gesture
     * @return bool, true if the profile is already learned, false if not
     */
    public function isLearnead(Gesture $gesture, Profile $profile) : bool
    {

        if($gesture->getId() && $profile->getId()){

            $learnedGestures = $this->manager->getRepository(ProfileGesture::class)->findBy([
                'profile'=>$profile->getId(),
                'gesture'=>$gesture->getId()
            ]);
            if(empty($learnedGestures))
            {
                return false;
            }

        }

        return true; //not addable

    }
}