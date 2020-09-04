<?php

namespace App\Form;

use App\Dto\Role\RoleDto;
use App\Service\Role\RoleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * RoleType constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
            ])
            ->add('translatable', CollectionType::class, [
                'label' => false,
                'entry_type' => TranslationType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'parent',
                'placeholder' => 'Choose an option',
                'choices' => $this->getRoles($options['data']),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
                'translation_domain' => 'fields',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleDto::class,
            'translation_domain' => 'role',
        ]);
    }

    /**
     * @param RoleDto $roleDto
     *
     * @return array
     */
    public function getRoles(RoleDto $roleDto): array
    {
        return array_filter($this->roleService->getRoleForForm(), function ($key) use ($roleDto) {
            return $roleDto->uuid != $key;
        });
    }
}
