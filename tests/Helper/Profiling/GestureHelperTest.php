<?php

namespace App\Tests\Helper\Profiling;

use App\Entity\Thesaurus\Gesture;
use App\Helper\Profiling\GestureHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class GestureHelperTest extends TestCase
{
    public function testNonExistingGesture()
    {
        $gestureHelper = $this->getMockedGestureHelper();
        $gesture = new Gesture();
        $gesture->setId(7);

        $exist = $gestureHelper->isExisting($gesture);
        $this->assertFalse($exist);
    }

    public function testExistingGesture()
    {
        $gesture = new Gesture();
        $gesture->setId(84);
        $gestureHelper = $this->getMockedGestureHelper($gesture);
        $gesture = new Gesture();
        $gesture->setId(7);

        $exist = $gestureHelper->isExisting($gesture);
        $this->assertFalse($exist);

        $gesture->setId(84);

        $exist = $gestureHelper->isExisting($gesture);
        $this->assertTrue($exist);
    }

    public function testNotPublishedGesture()
    {
        $gesture = new Gesture();
        $gesture->setId(84);
        $gestureHelper = $this->getMockedGestureHelper($gesture);
        $gesture = new Gesture();
        $gesture->setId(7);

        $published = $gestureHelper->isPublished($gesture);
        $this->assertFalse($published);
    }

    public function testPublishedGesture()
    {
        $gesture = new Gesture();
        $gesture->setId(84)->setIsPublished(true);
        $gestureHelper = $this->getMockedGestureHelper($gesture);
        $gesture = new Gesture();
        $gesture->setId(7);

        $published = $gestureHelper->isPublished($gesture);
        $this->assertTrue($published);
    }

    public function getMockedGestureHelper(Gesture $gesture = null){

        $gestureRepository = $this->createMock(ObjectRepository::class);

        $gestureRepository->expects($this->any())
            ->method('find')
            ->willReturn($gesture);

        $em = $this->createMock(ObjectManager::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($gestureRepository);

        return new GestureHelper($em);
    }
}
