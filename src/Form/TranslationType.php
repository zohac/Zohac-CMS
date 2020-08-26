<?php

namespace App\Form;

use App\Dto\Translation\TranslationDto;
use App\Service\Language\LanguageService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationType extends AbstractType
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * TranslationType constructor.
     *
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->languageService->getLanguagesForForm();

        $builder
            ->add('message', TextType::class, [
                'label' => 'message',
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'language',
                'choices' => $languages,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslationDto::class,
            'translation_domain' => 'translation',
        ]);
    }
}
