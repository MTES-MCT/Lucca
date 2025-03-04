<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ParameterBundle\Entity\Partner;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, ['label' => 'label.code'])
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
            'translation_domain' => 'LuccaParameterBundle',
            'required' => true
        ]);
    }
}
