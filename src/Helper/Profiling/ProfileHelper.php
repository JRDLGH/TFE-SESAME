<?php

namespace App\Helper\Profiling;

use App\Entity\Profiling\Profile;
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

            $storedProfile = $this->manager->getRepository(Profile::class)->find($profile->getId());
            if($storedProfile){
                if($storedProfile->getId() === $profile->getId())
                {
                    $exists = true;
                }
            }
        }

        return $exists;
    }

}