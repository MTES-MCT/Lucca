<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Lucca\Bundle\MediaBundle\Entity\{Media, Category};
use Lucca\Bundle\MediaBundle\EventListener\MediaFormListener;
use Lucca\Bundle\MediaBundle\Form\MetaData\MetaDataType;
use Lucca\Bundle\MediaBundle\Repository\CategoryRepository;

class MediaType extends AbstractType
{
    public function __construct(
        private readonly MediaFormListener $mediaFormListener,
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'label.uploadFile',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'mediaFile']])
            ->add('public', CheckboxType::class, ['label' => 'label.public', 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false])
            ->add('metas', CollectionType::class, [
                'label' => 'label.metaData', 'entry_type' => MetaDataType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => ['attr' => ['class' => 'metaData']],
                'attr' => ['class' => 'table table-striped table-hover metaData-collection'],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class, 'choice_label' => 'formLabel', 'required' => false,
                'multiple' => false, 'expanded' => false, 'label' => 'label.category', 'attr' => ['class' => 'select2'],
                'query_builder' => function (CategoryRepository $repo): QueryBuilder {
                    return $repo->getValuesActive();
                }
            ]);

        $builder
            ->addEventSubscriber($this->mediaFormListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'translation_domain' => 'LuccaMediaBundle',
            'required' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_media';
    }
}
