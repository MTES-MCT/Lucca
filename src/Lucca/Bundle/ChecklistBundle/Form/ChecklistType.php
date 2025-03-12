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
use Symfony\Component\Form\Extension\Core\Type\{TextType, TextareaType, CheckboxType, CollectionType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ChecklistBundle\Entity\Checklist;

class ChecklistType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('description', TextareaType::class, [
                'label' => 'label.description', 'required' => false, 'attr' => ['class' => 'summernote']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    Checklist::STATUS_MINUTE => Checklist::STATUS_MINUTE,
                    Checklist::STATUS_UPDATING => Checklist::STATUS_UPDATING,
                ], 'label' => 'label.status', 'expanded' => false, 'required' => false
            ])
            ->add('elements', CollectionType::class, [
                'label' => 'label.elements', 'entry_type' => ElementType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => ['attr' => ['class' => 'element']],
                'attr' => ['class' => 'table element-collection'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Checklist::class,
            'translation_domain' => 'ChecklistBundle',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_checklistbundle_checklist';
    }
}
