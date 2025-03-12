<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Admin;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CollectionType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Entity\{Category, Storager};
use Lucca\Bundle\MediaBundle\Form\MetaDataModel\MetaDataModelType;
use Lucca\Bundle\MediaBundle\Form\Extension\ExtensionType;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name'))
            ->add('metasDatasModels', CollectionType::class, [
                'label' => 'label.metaDataModel', 'entry_type' => MetaDataModelType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => ['attr' => ['class' => 'metaDataModel']],
                'attr' => ['class' => 'table table-striped table-hover metaDataModel-collection'],
            ])
            ->add('extensions', CollectionType::class, [
                'label' => 'label.extension', 'entry_type' => ExtensionType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => ['attr' => ['class' => 'extension']],
                'attr' => ['class' => 'table table-striped table-hover extension-collection'],
            ])
            ->add('storager', EntityType::class, [
                'class' => Storager::class, 'choice_label' => 'formLabel', 'required' => true,
                'multiple' => false, 'expanded' => false, 'label' => 'label.storager',
                'attr' => ['class' => 'select2'],
            ])
            ->add('icon', TextType::class, ['label' => 'label.icon', 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'translation_domain' => 'MediaBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_category';
    }
}
