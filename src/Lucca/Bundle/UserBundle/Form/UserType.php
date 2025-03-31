<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\Extension\Core\Type\{TextType, EmailType, CheckboxType, RepeatedType, PasswordType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\UserBundle\Entity\{User, Group};
use Lucca\Bundle\UserBundle\Repository\GroupRepository;

class UserType extends AbstractType
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => 'label.username'])
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, 'required' => false,
                'first_options' => ['label' => 'label.password'],
                'second_options' => ['label' => 'label.passwordConfirmation'],
                'invalid_message' => 'user.password.mismatch',
            ])
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => false]);

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('groups', EntityType::class, [
                'class' => Group::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'expanded' => true, 'label' => 'label.groups'
            ]);
        } else {
            $builder->add('groups', EntityType::class, [
                'class' => Group::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'expanded' => true, 'label' => 'label.groups',
                'query_builder' => function (GroupRepository $repo) {
                    return $repo->getFormGroup();
                }
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'UserBundle',
            'required' => true
        ]);
    }
}
