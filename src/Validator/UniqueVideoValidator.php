<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueVideoValidator extends ConstraintValidator
{
    public function validate($gesture, Constraint $constraint)
    {
        /* @var $constraint App\Validator\UniqueVideo */

        $video = $gesture->getVideoFile();
        $profileVideo= $gesture->getProfileVideoFile();

        $violation = $this->hasViolation($video,$profileVideo,$gesture->getProfileVideo());

        if(!$violation)
        {
            $violation = $this->hasViolation($profileVideo,$video,$gesture->getVideo());
        }


        if($violation)
        {
            $source = $this->getViolationSource($gesture);
            $this->context->buildViolation($constraint->message)
                ->atPath($source)
                ->addViolation();
        }


    }


    private function getViolationSource($gesture) : string
    {
        if(!empty($gesture->getVideoFile()))
        {
            return 'videoFile';
        }else
        {
            return 'profileVideoFile';
        }
    }

    private function hasViolation($source,$target,$targetVideoName)
    {
        $violation = false;

        $sourceName = $this->getOriginalFileName($source);
        $targetName = $this->getOriginalFileName($target);

        if($source !== null){
            if($source === $target){
                $violation = true;
            }
            else if($sourceName === $targetName)
            {
                $violation = true;
            }
            else if($sourceName == $targetVideoName)
            {
                $violation = true;
            }
        }


        return $violation;
    }

    private function getOriginalFileName($file)
    {
        if($file){
            return $file->getClientOriginalName();
        }
    }
}
