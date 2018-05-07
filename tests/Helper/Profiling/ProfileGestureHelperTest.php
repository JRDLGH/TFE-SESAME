<?php

namespace App\Tests\Helper\Profiling;

use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use App\Helper\Profiling\ProfileGestureHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ProfileGestureHelperTest extends TestCase
{
    public function testAlreadyLearnedGesture()
    {
        $profile = new Profile();
        $profile->setId(1);

        $gesture = new Gesture();
        $gesture->setName('hello');
        $gesture->setDescription('hello');
        $gesture->setId(10);
        $gesture->setIsPublished(true);

        $profileGesture = new ProfileGesture();
        $profileGesture->setProfile($profile);
        $profileGesture->setGesture($gesture);


        $helper =  $this->getMockedProfileGestureHelper($profileGesture);
        $learned = $helper->isLearnead($gesture,$profile);
        $this->assertTrue($learned);
    }

    public function testNotAlreadyLearnedGesture()
    {
        $profile = new Profile();
        $profile->setId(1);

        $gesture = new Gesture();
        $gesture->setName('hello');
        $gesture->setDescription('hello');
        $gesture->setId(10);
        $gesture->setIsPublished(true);

        $profileGesture = new ProfileGesture();
        $profileGesture->setProfile($profile);
        $profileGesture->setGesture($gesture);

        $helper =  $this->getMockedProfileGestureHelper();

        $gesture->setId(15);
        $learned = $helper->isLearnead($gesture,$profile);
        $this->assertFalse($learned);
    }

    public function getMockedProfileGestureHelper(ProfileGesture $profileGesture = null)
    {
        //Le mock permet de ne plus dépendre de la base de données.
        $profileGestureRepository = $this->createMock(ObjectRepository::class);


        $profileGestureRepository->expects($this->any())
            ->method('find')
            ->willReturn($profileGesture);

        $em = $this->createMock(ObjectManager::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($profileGestureRepository);

        return new ProfileGestureHelper($em);
    }
}
