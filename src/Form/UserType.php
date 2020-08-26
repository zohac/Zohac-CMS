<?php

namespace App\Form;

use App\Dto\User\UserDto;
use App\Service\Language\LanguageService;
use App\Service\Role\RoleService;
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
    /**.
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(LanguageService $languageService, RoleService $roleService)
    {
        $this->languageService = $languageService;
        $this->roleService = $roleService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var UserDto $userDto */
        $userDto = $options['data'];
        $languages = $this->languageService->getLanguagesForForm();
        $roles = $this->roleService->getRoleForForm();

        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'roles',
                'choices' => $roles,
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
                'choices' => $languages,
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
