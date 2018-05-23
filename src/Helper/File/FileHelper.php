<?php
/**
 * Created by PhpStorm.
 * User: JRDN
 * Date: 23/05/2018
 * Time: 09:14
 */

namespace App\Helper\File;


class FileHelper
{

    public function exists($imagePath) : bool
    {
        $exist = false;

        if($imagePath != null)
        {
            return file_exists($imagePath);
        }

        return $exist;

    }

}