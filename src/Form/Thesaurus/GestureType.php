<?php

namespace App\Form\Thesaurus;

use App\Entity\Thesaurus\Gesture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('profileVideo')
            ->add('video')
            ->add('cover')
            ->add('description')
//            ->add('creationDate')
            ->add('isPublished')
//            ->add('publicationDate')
            ->add('tags')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gesture::class,
        ]);
    }
}
