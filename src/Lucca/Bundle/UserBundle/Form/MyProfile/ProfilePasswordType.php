<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Form\MyProfile;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

use Lucca\Bundle\UserBundle\Entity\User;

class ProfilePasswordType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('currentPassword', PasswordType::class, [
            'label' => 'label.currentPassword', 'mapped' => false,
            'constraints' => [
                new NotBlank(),
                new UserPassword(),
            ]
        ]);

        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, 'required' => true,
                'first_options' => ['label' => 'label.password', 'attr' => ['class' => 'plainPassword_first']],
                'second_options' => ['label' => 'label.passwordConfirmation', 'attr' => ['class' => 'plainPassword_second']],
                'invalid_message' => 'constraint.user.password.mismatch',
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'translation_domain' => 'UserBundle',
            'required' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_userBundle_profile';
    }
}

