<?php

namespace App\Tests\Helper\File;

use App\Helper\File\FileHelper;
use PHPUnit\Framework\TestCase;

class ImageHelperTest extends TestCase
{
    public function testNotExistingImageNotExist()
    {
        $image = "C:/wamp64/www/sesame.be/public/thesaurus/gestures/images/notimage.jpg";

        $imageHelper = new FileHelper();

        $this->assertFalse($imageHelper->exists($image));
    }

    public function testExistingImageExists()
    {
        $image = "C:/wamp64/www/sesame.be/public/thesaurus/gestures/images/IMG_20150723_195154.jpg";

        $imageHelper = new FileHelper();

        $this->assertTrue($imageHelper->exists($image));
    }

    public function testShorterPathToFileExist()
    {
        $image = "public/thesaurus/gestures/images/IMG_20150723_195154.jpg";

        $imageHelper = new FileHelper();

        $this->assertTrue($imageHelper->exists($image));
    }

}
