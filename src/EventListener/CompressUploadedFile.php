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

    public function onPreUpload(Event $event)
    {
        $entity = $event->getObject();
        $this->compressImage($entity->getCoverFile());
    }

    public function compressImage($file)
    {

        Tinify::setKey(self::API_KEY);

        $source = \Tinify\fromFile($file->getLinkTarget());

        $source->toFile($file->getPathName());

    }

}