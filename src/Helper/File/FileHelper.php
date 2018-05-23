<?php
/**
 * Created by PhpStorm.
 * User: JRDN
 * Date: 23/05/2018
 * Time: 09:14
 */

namespace App\Helper\File;

use App\Entity\Thesaurus\Gesture;
use Symfony\Component\HttpFoundation\File\File;

class FileHelper
{

    const DEFAULT_COVER = '/thesaurus/gestures/images/default.jpg';

    /**
     * Verify if the file exists at the given location
     * @param $path
     * @return bool
     */
    public function exists($path) : bool
    {
        $exist = false;

        if($path != null)
        {
            return file_exists($path);
        }

        return $exist;

    }

    //MUST SEND URL LIKE THIS: /thesaurus/gestures/images\IMG_20150723_195154.jpg
    public function getFilePath(?File $file) : ?string
    {
        if($file)
        {
            $path = $file->getPathName();

            if($this->exists($path))
            {
                $path = str_replace('\\','/',$path);

                $explodedPath = explode('/',$path);

                if(in_array('public',$explodedPath))
                {
                    $beginAt = array_search('public',$explodedPath)+1;
                    $explodedPath = array_slice($explodedPath,$beginAt,count($explodedPath));
                    $path = "/".implode("/",$explodedPath);
                }
                return $path;
            }
        }

        return null;

    }

    public function setGestureVideoPath(Array $gestures)
    {
        foreach ($gestures as $gesture){
            $video = $this->getFilePath($gesture->getVideoFile());
            if($video)
            {
                $gesture->setVideo($video);
            }
        }
    }

    public function setGestureProfileVideoPath(Array $gestures)
    {
        foreach ($gestures as $gesture){
            $profileVideo = $this->getFilePath($gesture->getProfileVideoFile());
            if($profileVideo)
            {
                $gesture->setProfileVideo($profileVideo);
            }
        }
    }

    /**
     * @param Gesture[] $gestures
     */
    public function setGesturesCoverPath(Array $gestures)
    {
        foreach ($gestures as $gesture){
            $cover = $this->getFilePath($gesture->getCoverFile());
            if($cover)
            {
                $gesture->setCover($cover);
            }
            else
            {
                $gesture->setCover(self::DEFAULT_COVER);
            }
        }
    }

}