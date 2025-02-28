<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form\Courier;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourierOffenderType
 *
 * @package Lucca\MinuteBundle\Form\Courier
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierOffenderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('humansEditions', CollectionType::class, array(
                'label' => 'label.editions', 'entry_type' => CourierOffenderEditionType::class,
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
            'data_class' => Courier::class,
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_courier_offender';
    }
}
