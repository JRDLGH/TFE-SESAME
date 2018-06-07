<?php

namespace App\Helper\Profiling;

use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use Doctrine\Common\Persistence\ObjectManager;

class ProfileHelper{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function isAccessible(){
        //TODO
    }

    /**
     * @param Profile $profile
     * @return bool
     */
    public function isExisting(Profile $profile) : bool
    {
        $exists = false;

        if($profile->getId())
        {

            $storedProfile = $this->getCompleteProfile($profile);
            if($storedProfile){
                if($storedProfile->getId() === $profile->getId())
                {
                    $exists = true;
                }
            }
        }

        return $exists;
    }

    public function getCompleteProfile(Profile $profile) : ?Profile
    {
        if($profile->getId()){

            $profile = $this->manager->getRepository(Profile::class)->find($profile->getId());


        }

        return $profile;
    }

    public function addLearnedGesture(Profile $profile, Gesture $gesture,ProfileGestureHelper $profileGestureHelper) : Profile
    {

        if(!$profileGestureHelper->isLearnead($gesture,$profile))
        {
            $profile = $this->getCompleteProfile($profile);

            $profileGesture = new ProfileGesture();
            $profileGesture->setProfile($profile);
            $profileGesture->setGesture($gesture);
            $profileGesture->setLearningDate(new \DateTime("now",new \DateTimeZone("Europe/Brussels")));



            $profile->addLearnedGesture($profileGesture);

            return $profile;
        }

        return $profile;
    }
}