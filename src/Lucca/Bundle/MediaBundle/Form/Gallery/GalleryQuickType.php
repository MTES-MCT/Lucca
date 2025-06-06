<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Gallery;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Entity\Gallery;
use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Lucca\Bundle\MediaBundle\Form\Dropzone\DropzoneType;

class GalleryQuickType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('defaultMedia', MediaQuickType::class, ['required' => false])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false])
            ->add('files', DropzoneType::class, [
                'label' => 'label.medias', 'required' => false, 'mapped' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
            'translation_domain' => 'MediaBundle',
            'required' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_gallery';
    }
}
