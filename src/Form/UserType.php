<?php

namespace App\Form;

use App\Dto\User\UserDto;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ])
            ->add('roles', EntityType::class, [
                'label' => 'roles',
                'class' => Role::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'password'],
                'second_options' => ['label' => 'repeat password'],
            ])
            ->add('locale', TextType::class, [
                'label' => 'locale',
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
