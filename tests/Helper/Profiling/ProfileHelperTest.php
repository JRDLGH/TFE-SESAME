<?php

namespace App\Tests\Helper\Profiling;

use App\Entity\Profiling\Profile;
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

    /**
     * @param array $defaultTags
     * @return TagsTransformer
     */
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
            ->willReturn($profileRepository);

        return new ProfileHelper($em);
    }
}
