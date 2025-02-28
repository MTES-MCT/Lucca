<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\Form\Admin;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, ChoiceType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Entity\Storager;
use Lucca\Bundle\MediaBundle\Namer\{FolderNamerInterface, MediaNamerInterface};

class StoragerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('serviceFolderNaming', ChoiceType::class, [
                'choices' => [
                    FolderNamerInterface::NAMER_FOLDER => FolderNamerInterface::NAMER_FOLDER,
                    FolderNamerInterface::NAMER_FOLDER_BY_DATE => FolderNamerInterface::NAMER_FOLDER_BY_DATE,
                ],
                'label' => 'label.serviceFolderNaming', 'required' => true
            ])
            ->add('serviceMediaNaming', ChoiceType::class, [
                'choices' => [
                    MediaNamerInterface::NAMER_MEDIA => MediaNamerInterface::NAMER_MEDIA,
                ],
                'label' => 'label.serviceMediaNaming', 'required' => true
            ])
            ->add('description', TextareaType::class, ['label' => 'label.description', 'required' => false,
                'attr' => ['class' => 'summernote']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Storager::class,
            'translation_domain' => 'LuccaMediaBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_mediaBundle_storager';
    }
}
