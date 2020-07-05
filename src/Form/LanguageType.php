<?php

namespace App\Form;

use App\Dto\Language\LanguageDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
            ])
            ->add('alternateName', TextType::class, [
                'label' => 'alternate name',
                'required' => false,
            ])
            ->add('description', TextType::class, [
                'label' => 'description',
                'required' => false,
            ])
            ->add('iso639_1', TextType::class, [
                'label' => 'iso639-1',
                'required' => false,
            ])
            ->add('iso639_2T', TextType::class, [
                'label' => 'iso639-2/T',
                'required' => false,
            ])
            ->add('iso639_3', TextType::class, [
                'label' => 'iso639-3',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
                'translation_domain' => 'fields',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LanguageDto::class,
            'translation_domain' => 'language',
        ]);
    }
}
