<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form\Control;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ControlAccessType
 *
 * @package Lucca\MinuteBundle\Form\Control
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlAccessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('editions', CollectionType::class, array(
                'label' => 'label.editions', 'entry_type' => ControlEditionAccessType::class,
                'allow_add' => false, 'allow_delete' => false, 'delete_empty' => false,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => array('attr' => array('class' => 'element')),
                'attr' => array('class' => 'table editions-collection'),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Control::class,
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_control_access';
    }
}
