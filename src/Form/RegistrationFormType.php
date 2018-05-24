<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
        ->add('firstname',null, array('label' => 'form.firstname', 'translation_domain' => 'FOSUserBundle'))
        ->add('lastname',null, array('label' => 'form.lastname', 'translation_domain' => 'FOSUserBundle'));
    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }
}