<?php

namespace App\Validator;

use App\Helper\Profiling\GestureHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PublishableGestureValidator extends ConstraintValidator
{
    public function validate($gesture, Constraint $constraint)
    {
        /* @var $constraint App\Validator\PublishableGesture */
        if($gesture->getIsPublished())
        {

            if( !($gesture->getCoverFile() || $gesture->getCover())
                && !($gesture->getProfileVideoFile() || $gesture->getProfileVideo())
                && !($gesture->getVideoFile() || $gesture->getVideo()) )
            {

                $this->context->buildViolation($constraint->message)
                    ->atPath('isPublished')
                    ->addViolation();
            }

        }

    }
}
