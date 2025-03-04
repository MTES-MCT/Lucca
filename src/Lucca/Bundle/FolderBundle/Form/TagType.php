<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Lucca\Bundle\FolderBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name', 'required' => true))
            ->add('num', IntegerType::class, array('label' => 'label.num', 'required' => true))
            ->add('enabled', CheckboxType::class, array('label' => 'label.enabled'))
            ->add('description', TextareaType::class, array('label' => 'label.description',
                'attr' => array('class' => 'summernote')))
            ->add('proposals', CollectionType::class, array(
                'label' => 'label.proposals', 'entry_type' => ProposalType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => array('attr' => array('class' => 'element')),
                'attr' => array('class' => 'table proposals-collection'),
            ))
            ->add('category', ChoiceType::class, array(
                'choices' => array(
                    Tag::CATEGORY_NATURE => Tag::CATEGORY_NATURE,
                    Tag::CATEGORY_TOWN => Tag::CATEGORY_TOWN,
                ),
                'label' => 'label.category', 'expanded' => false, 'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Tag::class,
            'translation_domain' => 'LuccaFolderBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_tag';
    }
}
