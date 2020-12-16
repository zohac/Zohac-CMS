<?php

namespace App\Form;

use App\Dto\Maintenance\MaintenanceDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $maintenanceDto = $options['data'];

        $builder
            ->add('redirectPath', TextType::class, [
                'label' => 'redirect path',
            ])
            ->add('mode', ChoiceType::class, [
                'label' => 'maintenance',
                'choices' => ['activate' => true],
                'data' => ['activate' => $maintenanceDto->mode],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('ips', CollectionType::class, [
                'label' => 'Authorized IP address',
                'entry_type' => TextType::class,
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
            'data_class' => MaintenanceDto::class,
            'translation_domain' => 'maintenance',
        ]);
    }
}
