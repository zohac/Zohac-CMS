<?php

namespace App\Form;

use App\Dto\User\UserDto;
use App\Repository\LanguageRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(LanguageRepository $languageRepository, RoleRepository $roleRepository)
    {
        $this->languageRepository = $languageRepository;
        $this->roleRepository = $roleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var UserDto $userDto */
        $userDto = $options['data'];

        $languages = $this->languageRepository->findAll();

        $languagesChoices = [];
        foreach ($languages as $language) {
            $languagesChoices[$language->getIso6391()] = $language->getUuid();
        }

        $roles = $this->roleRepository->findAll();

        $rolesChoices = [];
        foreach ($roles as $role) {
            $rolesChoices[$role->getName()] = $role->getUuid();
        }

        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'roles',
                'choices' => $rolesChoices,
                'data' => $userDto->roles,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'password'],
                'second_options' => ['label' => 'repeat password'],
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'locale',
                'choices' => $languagesChoices,
                'data' => $userDto->language,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
                'translation_domain' => 'fields',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDto::class,
            'translation_domain' => 'user',
        ]);
    }
}
