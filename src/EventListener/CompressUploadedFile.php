<?php
/**
 * Created by PhpStorm.
 * User: JRDN
 * Date: 22/05/2018
 * Time: 16:10
 */

namespace App\EventListener;



use function Tinify\fromFile;
use Tinify\Tinify;
use Vich\UploaderBundle\Entity\File;
use Vich\UploaderBundle\Event\Event;

class CompressUploadedFile
{

    const API_KEY = 'OqcIhWbV-QWiCBYJznJ4pdosMISjqudd';
    const IMG_MAX_WIDTH = 500;
    const IMG_MAX_HEIGHT = 500;

    public function onPreUpload(Event $event)
    {
        $entity = $event->getObject();

        $this->compressImage($entity->getCoverFile());
    }

    /**
     * Compress the temporary file used and replace it by it's compressed version.
     * @param $image
     */
    public function compressImage($image)
    {

        Tinify::setKey(self::API_KEY);

        list($width,$height) = getimagesize($image);

        $source = \Tinify\fromFile($image->getLinkTarget());

        if($width > self::IMG_MAX_WIDTH || $height > self::IMG_MAX_HEIGHT)
        {
            $source = $this->resizeImage($source);
        }

        $source->toFile($image->getPathName());

    }

    /**
     *
     * @param $source
     * @return mixed
     */
    public function resizeImage($source)
    {

        return $source->resize(array(
            "method" => "scale",
            "width" => 500,
        ));

    }

}