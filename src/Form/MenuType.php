<?php

namespace App\Form;

use App\Dto\Menu\MenuDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('items', CollectionType::class, [
                'label' => false,
                'entry_type' => MenuItemType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
            ])

            ->add('save', SubmitType::class, [
                'label' => 'save',
                'translation_domain' => 'fields',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuDto::class,
            'translation_domain' => 'menu',
        ]);
    }
}
