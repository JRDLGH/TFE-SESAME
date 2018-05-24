<?php

namespace App\Tests\Helper\Profiling;

use App\Entity\Profiling\Profile;
use App\Entity\Profiling\ProfileGesture;
use App\Entity\Thesaurus\Gesture;
use App\Helper\Profiling\GestureHelper;
use App\Helper\Profiling\ProfileGestureHelper;
use App\Helper\Profiling\ProfileHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ProfileHelperTest extends TestCase
{
    public function testNonexistingProfile()
    {
        $profileHelper = $this->getMockedProfileHelper();
        $profile = new Profile();
        $exists = $profileHelper->isExisting($profile);
        $this->assertFalse($exists);
    }

    public function testExistingProfileExist(){
        $profile = new Profile();
        $profile->setId(1);

        $profileHelper = $this->getMockedProfileHelper($profile);

        $profile = new Profile();

        $profile->setId(1);
        $exist = $profileHelper->isExisting($profile);
        $this->assertTrue($exist);

        $profile->setId(17);
        $exist = $profileHelper->isExisting($profile);
        $this->assertFalse($exist);
    }

    public function testNonAlreadyLearnedGesture()
    {
        $profile = new Profile();
        $profile->setId(intval(1));

        $gesture = new Gesture();
        $gesture->setId(79);
        $gesture->setName('Bonjour');
        $gesture->setIsPublished(true);

        $profileGesture = new ProfileGesture();
        $profileGesture->setGesture($gesture);
        $profileGesture->setProfile($profile);

        $profileHelper = $this->getMockedProfileHelper($profile);
        $profileGestureHelper = new ProfileGestureHelperTest();

        $profileGestureHelper = $profileGestureHelper->getMockedProfileGestureHelper($profileGesture);

        $updatedProfile = $profileHelper->addLearnedGesture($profile,$gesture,$profileGestureHelper);

        $this->assertTrue($updatedProfile === $profile);

    }

    public function testAlreadyLearnedGesture()
    {
        $profile = new Profile();
        $profile->setId(intval(1));

        $gesture = new Gesture();
        $gesture->setId(79);
        $gesture->setName('Bonjour');
        $gesture->setIsPublished(true);

        $profileGesture = new ProfileGesture();
        $profileGesture->setGesture($gesture);
        $profileGesture->setProfile($profile);

//        $profile->addLearnedGesture($profileGesture);

        $profileHelper = $this->getMockedProfileHelper($profile);
        $profileGestureHelper = new ProfileGestureHelperTest();

        $mockedProfileGestureHelper = $profileGestureHelper->getMockedProfileGestureHelper();

        $learned = $mockedProfileGestureHelper->isLearnead($gesture,$profile);
        $this->assertFalse($learned);

        $mockedProfileGestureHelper = $profileGestureHelper->getMockedProfileGestureHelper($profileGesture);

        $updatedProfile = $profileHelper->addLearnedGesture($profile,$gesture,$mockedProfileGestureHelper);

        $this->assertTrue($updatedProfile === $profile);
        $learned = $mockedProfileGestureHelper->isLearnead($gesture,$profile);
        $this->assertTrue($learned);
    }

    private function getMockedProfileHelper(Profile $profile = null)
    {
        //Le mock permet de ne plus dépendre de la base de données.
        $profileRepository = $this->createMock(ObjectRepository::class);


        $profileRepository->expects($this->any())
            ->method('find')
            ->willReturn($profile);

        $em = $this->createMock(ObjectManager::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->with('App\Entity\Profiling\Profile')
            ->willReturn($profileRepository);

        return new ProfileHelper($em);
    }
}
