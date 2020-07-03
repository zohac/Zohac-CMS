<?php

namespace App\Form;

use App\Dto\User\UserDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var UserDto $userDto */
        $userDto = $options['data'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
                'translation_domain' => 'user',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'roles',
                'translation_domain' => 'user',
                'choices' => [
                    'ROLE_USER' => 0,
                    'ROLE_ADMIN' => 1,
                ],
                'expanded' => true,
                'multiple' => true,
                'data' => array_keys($userDto->roles),
            ])
            ->add('password', RepeatedType::class, [
                'label' => false,
                'translation_domain' => 'user',
                'type' => PasswordType::class,
                'first_options' => ['label' => 'password'],
                'second_options' => ['label' => 'repeat password'],
            ])
            ->add('locale', TextType::class, [
                'label' => 'locale',
                'translation_domain' => 'user',
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
        ]);
    }
}
