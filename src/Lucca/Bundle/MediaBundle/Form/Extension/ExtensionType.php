<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Extension;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Entity\Extension;

class ExtensionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('value', TextType::class, ['label' => 'label.value', 'required' => true])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Extension::class,
            'translation_domain' => 'LuccaMediaBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_extension';
    }
}
