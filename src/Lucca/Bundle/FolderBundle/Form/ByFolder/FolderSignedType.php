<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form\ByFolder;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FolderSignedType
 *
 * @package Lucca\Bundle\FolderBundle\Form\ByFolder
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class FolderSignedType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('folderSigned', MediaQuickType::class, array('label' => 'label.file', 'required' => false));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
            'translation_domain' => 'LuccaFolderBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_foldersigned';
    }
}
