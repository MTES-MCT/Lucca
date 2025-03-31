<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, ChoiceType, CollectionType, IntegerType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\FolderBundle\Entity\Tag;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name', 'required' => true))
            ->add('num', IntegerType::class, array('label' => 'label.num', 'required' => true))
            ->add('enabled', CheckboxType::class, array('label' => 'label.enabled'))
            ->add('description', TextareaType::class, array('label' => 'label.description',
                'attr' => array('class' => 'summernote')))
            ->add('proposals', CollectionType::class, array(
                'label' => 'label.proposals', 'entry_type' => ProposalType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => array('attr' => array('class' => 'element')),
                'attr' => array('class' => 'table proposals-collection'),
            ))
            ->add('category', ChoiceType::class, array(
                'choices' => array(
                    Tag::CATEGORY_NATURE => Tag::CATEGORY_NATURE,
                    Tag::CATEGORY_TOWN => Tag::CATEGORY_TOWN,
                ),
                'label' => 'label.category', 'expanded' => false, 'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Tag::class,
            'translation_domain' => 'FolderBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_folderBundle_tag';
    }
}
