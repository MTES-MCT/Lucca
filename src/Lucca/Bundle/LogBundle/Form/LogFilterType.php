<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Form;

use Symfony\Component\Form\{AbstractType, Extension\Core\Type\TextType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\LogBundle\Entity\Log;

class LogFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    Log::STATUS_INSERT => Log::STATUS_INSERT,
                    Log::STATUS_UPDATE => Log::STATUS_UPDATE,
                    Log::STATUS_REMOVE => Log::STATUS_REMOVE,
                    Log::STATUS_CONNECTION => Log::STATUS_CONNECTION,
                ),
                'label' => 'label.status', 'expanded' => false,
                'required' => false,
            ))
            ->add('shortMessage', TextType::class, array(
                'label' => 'label.short_message', 'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Log::class,
            'translation_domain' => 'LogBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_folderBundle_logFilter';
    }
}
