<?php

namespace App\Form\Thesaurus;

use App\Entity\Thesaurus\Gesture;
use App\Form\Type\TagsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description',TextareaType::class)
            ->add('isPublished')
            ->add('tags',TagsType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gesture::class,
        ]);
    }
}
