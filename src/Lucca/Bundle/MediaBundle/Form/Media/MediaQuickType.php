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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use Lucca\Bundle\MediaBundle\Entity\Media;
use Lucca\Bundle\MediaBundle\EventListener\MediaFormListener;
use Lucca\Bundle\MediaBundle\Form\Dropzone\DropzoneType;

class MediaQuickType extends AbstractType
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
        if ($options['multiple']) {
            $builder
                ->add('files', DropzoneType::class, ['label' => 'label.uploadFile', 'mapped' => false, 'required' => false]);
        }
        elseif ($options['isImage']) {
            $builder
                ->add('file', FileType::class, ['label' => 'label.uploadFile', 'mapped' => false, 'required' => false,
                    'attr' => [
                        'class' => 'mediaFile',
                            'accept' => 'image/*'
                    ],
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Le fichier doit être une image.',
                        ])
                    ],
                ])
                ->addEventSubscriber($this->mediaFormListener);
        }
        else {
            $builder
                ->add('file', FileType::class, ['label' => 'label.uploadFile', 'mapped' => false, 'required' => false,
                    'attr' => ['class' => 'mediaFile']])
                ->addEventSubscriber($this->mediaFormListener);
        }
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
            'isImage' => false,
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
