<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\FolderBundle\Entity\Tag;
use Lucca\Bundle\FolderBundle\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FolderStep1Type
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderStep1Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ascertainment', TextareaType::class, array('label' => 'label.ascertainment', 'required' => false,
                'attr' => array('class' => 'summernote-light')
            ))
            ->add('tagsNature', EntityType::class, array(
                'class' => 'LuccaFolderBundle:Tag', 'choice_label' => 'name',
                'multiple' => true, 'expanded' => true, 'label' => 'label.nature', 'required' => true,
                'attr' => array('data-placeholder' => 'Choisissez un tag'),
                'query_builder' => function (TagRepository $er) {
                    return $er->getValuesByCategory(Tag::CATEGORY_NATURE);
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
            'translation_domain' => 'LuccaFolderBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_folder_step1';
    }
}
