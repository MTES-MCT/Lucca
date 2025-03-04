<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Control;

use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ControlEditionAccessType
 *
 * @package Lucca\Bundle\MinuteBundle\Form\Control
 * @author Terence <terence@numeric-wave.tech>
 */
class ControlEditionAccessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accessEdited', CheckboxType::class, array('label' => 'label.accessEdited', 'required' => false))
            ->add('letterAccess', TextareaType::class, array('label' => 'label.letterAccess', 'required' => true,
                'attr' => array('class' => 'summernote-letter')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ControlEdition::class,
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minuteBundle_controlEdition_access';
    }
}
