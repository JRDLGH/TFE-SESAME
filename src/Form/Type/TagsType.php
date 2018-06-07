<?php

namespace App\Form\Type;

use App\Form\DataTransformer\TagsTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsType extends AbstractType {


    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new CollectionToArrayTransformer(),true)
            ->addModelTransformer(new TagsTransformer($this->manager),true);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('attr',[
            'class' => 'tag-input',
            'data-toggle' => 'tagsinput'
        ]);
        //Set this input as non required by default
        $resolver->setDefault('required',false);
    }
}