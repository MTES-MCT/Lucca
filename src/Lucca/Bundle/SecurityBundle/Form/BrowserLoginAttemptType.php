<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Lucca\Bundle\SecurityBundle\Entity\LoginAttempt;

class BrowserLoginAttemptType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('requestedAt', DateType::class, array(
                'label' => 'label.requestedAt', 'required' => true,
                'widget' => 'single_text', 'input' => 'datetime', 'html5' => true
            ))
            ->add('requestIp', TextType::class, ['label' => 'label.requestIp'])
            ->add('requestUri', TextType::class, ['label' => 'label.requestUri'])
            ->add('username', TextType::class, ['label' => 'label.username'])
            ->add('address', TextType::class, ['label' => 'label.address'])
            ->add('addressRemote', TextType::class, ['label' => 'label.addressRemote'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => LoginAttempt::class,
            'translation_domain' => 'SecurityBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_securityBundle_loginAttempt';
    }
}
