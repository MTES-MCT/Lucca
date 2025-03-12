<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Dropzone;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\EventListener\DropzoneFormListener;

class DropzoneType extends AbstractType
{
    public function __construct(
        private readonly DropzoneFormListener $dropzoneFormListener,
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
                'label' => 'label.dropzone',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => (['hidden' => true])]);

        $builder
            ->addEventSubscriber($this->dropzoneFormListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArrayCollection::class,
            'translation_domain' => 'MediaBundle',
            'required' => false,
            'maxSize' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_dropzone';
    }
}
