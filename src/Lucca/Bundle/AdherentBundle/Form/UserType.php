<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{EmailType, RepeatedType, PasswordType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\UserBundle\Entity\{Group, User};
use Lucca\Bundle\UserBundle\Repository\GroupRepository;

class UserType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'label.email'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'UserBundle'),
                'first_options' => array('label' => 'label.password'),
                'second_options' => array('label' => 'label.passwordConfirmation'),
                'invalid_message' => 'user.password.mismatch',
                'required' => false
            ))
            ->add('groups', EntityType::class, array(
                'class' => Group::class, 'choice_label' => 'name',
                'multiple' => true, 'expanded' => true, 'label' => 'label.groups',
                'query_builder' => function (GroupRepository $er) {
                    return $er->getFormGroup();
                }
            ));
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
}
