<?php

namespace App\Form;

use App\Repository\LanguageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationType extends AbstractType
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * TranslationType constructor.
     *
     * @param LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->languageRepository->findAll();

        $languagesChoices = [];
        foreach ($languages as $language) {
            $languagesChoices[$language->getIso6391()] = $language->getUuid();
        }

        $builder
            ->add('message', TextType::class, [
                'label' => 'message',
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'language',
                'choices' => $languagesChoices,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'translation',
        ]);
    }
}
