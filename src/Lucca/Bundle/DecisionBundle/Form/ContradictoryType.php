<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form;

use Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Contradictory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContradictoryType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class ContradictoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateNoticeDdtm', DateType::class, array(
                'label' => 'label.dateNoticeDdtm', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateExecution', DateType::class, array(
                'label' => 'label.dateExecution', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('dateAnswer', DateType::class, array(
                'label' => 'label.dateAnswer', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('answer', TextareaType::class, array('label' => 'label.answer',
                'attr' => array('class' => 'summernote'), 'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Contradictory::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_contradictory';
    }
}
