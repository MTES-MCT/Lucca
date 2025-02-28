<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form\ByFolder;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\MediaBundle\Form\Dropzone\DropzoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnnexesType
 *
 * @package Lucca\MinuteBundle\Form\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class AnnexesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annexes', DropzoneType::class, array(
                'label' => 'label.annexes', 'required' => false, 'mapped' => false, 'maxSize' => "0k"
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minuteBundle_annexes';
    }
}
