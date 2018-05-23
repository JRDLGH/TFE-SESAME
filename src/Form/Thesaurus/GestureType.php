<?php

namespace App\Form\Thesaurus;

use App\Entity\Thesaurus\Gesture;
use App\Form\Type\TagsType;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GestureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('profileVideoFile', VichFileType::class,[
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => false,
                ])
            ->add('videoFile', VichFileType::class,[
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => false,
                ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => false,
            ])
            ->add('description',TextareaType::class, array(
                'required'=>false
                )
            )
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
