<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Lucca\Bundle\FolderBundle\Entity\ElementChecked;
use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ElementType
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class ElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', HiddenType::class, array('label' => 'label.position',
                'attr' => array('class' => 'collection-position', 'readonly' => true)
            ))
            ->add('state', CheckboxType::class, array(
                'label' => false, 'required' => true,
                'attr' => array('class' => 'js-state')
            ))
            ->add('name', TextType::class, array(
                'label' => false, 'required' => true,
                'attr' => array('readonly' => true, 'class' => 'js-name')
            ))
            ->add('image', MediaQuickType::class, array('label' => 'label.image', 'isImage' => true))
            ->add('comment', TextType::class, array(
                'label' => 'label.comment',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'label.comment'
                )));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ElementChecked::class,
            'translation_domain' => 'LuccaFolderBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_element';
    }
}
