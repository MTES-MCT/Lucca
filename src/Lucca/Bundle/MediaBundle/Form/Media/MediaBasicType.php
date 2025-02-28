<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Media;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, CollectionType, FileType, TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\EventListener\MediaFormListener;
use Lucca\Bundle\MediaBundle\Form\MetaData\MetaDataType;
use Lucca\Bundle\MediaBundle\Form\Dropzone\DropzoneType;

class MediaBasicType extends AbstractType
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
        /**
         * Multiple options as to be a bool
         * On True => show dropzoneType
         * On False => Show fileType
         */
        if($options['multiple']){
            $builder
                ->add('file', DropzoneType::class, ['label' => 'label.uploadFile', 'mapped' => false, 'required' => false]);
        } else {
            $builder
                ->add('file', FileType::class, ['label' => 'label.uploadFile', 'mapped' => false, 'required' => false,
                'attr' => ['class' => 'mediaFile']]);
        }
        $builder
            ->add('public', CheckboxType::class, ['label' => 'label.public', 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false])
            ->add('metas', CollectionType::class, [
                'label' => 'label.metaData', 'entry_type' => MetaDataType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => ['attr' => ['class' => 'metaData']],
                'attr' => ['class' => 'table table-striped table-hover metaData-collection'],
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
            'multiple' => false,
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
