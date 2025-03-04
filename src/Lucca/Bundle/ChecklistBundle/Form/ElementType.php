<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, NumberType, CheckboxType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ChecklistBundle\Entity\Element;

class ElementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => false, 'required' => false])
            ->add('position', NumberType::class, ['label' => 'label.position', 'required' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Element::class,
            'translation_domain' => 'LuccaChecklistBundle',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_checklistbundle_element';
    }
}
