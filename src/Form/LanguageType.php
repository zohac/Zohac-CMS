<?php

namespace App\Form;

use App\Dto\Language\LanguageDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('alternateName')
            ->add('description')
            ->add('iso639_1')
            ->add('iso639_2T')
            ->add('iso639_2B')
            ->add('iso639_3')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LanguageDto::class,
            'translation_domain' => 'language',
        ]);
    }
}
