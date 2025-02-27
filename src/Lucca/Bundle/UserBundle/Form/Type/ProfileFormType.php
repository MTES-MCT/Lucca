<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Form\Type;

use Lucca\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('current_password');

        $builder
            ->add('username', TextType::class, ['label' => 'label.username', 'attr' => [
                'readonly' => true
            ]])
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('name', TextType::class, ['label' => 'label.name']);

        $builder->add('current_password', PasswordType::class, options: array(
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => new UserPassword(),
            'required' => false,
        ));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'LuccaUserBundle',
            'required' => true
        ]);
    }

    /**
     * Get FOSUserBundle form parent
     * @return string
     */
    public function getParent(): string
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_user_profile';
    }
}
