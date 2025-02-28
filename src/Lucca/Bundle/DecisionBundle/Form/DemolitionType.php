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

use Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Demolition;
use Lucca\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DemolitionType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class DemolitionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', TextType::class, array('label' => 'label.company_intervening'))
            ->add('amountCompany', MoneyType::class, array('label' => 'label.amountCompany'))
            ->add('dateDemolition', DateType::class, array(
                'label' => 'label.dateDemolition', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('bailif', TextType::class, array('label' => 'label.bailif'))
            ->add('amountBailif', MoneyType::class, array('label' => 'label.amountBailif'))
            ->add('professions', CollectionType::class, [
                'label' => false, 'entry_type' => ProfessionType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => array('attr' => array('class' => 'profession')),
                'attr' => array('class' => 'table profession-collection'),
            ])
            ->add('comment', TextareaType::class, array('label' => 'label.comment',
                'attr' => array('class' => 'summernote'), 'required' => false
            ))
            ->add('result', ChoiceType::class, array(
                'choices' => array(
                    Demolition::RESULT_WAITING => Demolition::RESULT_WAITING,
                    Demolition::RESULT_REALIZED => Demolition::RESULT_REALIZED,
                    Demolition::RESULT_REPORTED => Demolition::RESULT_REPORTED,
                    Demolition::RESULT_CANCELLED => Demolition::RESULT_CANCELLED,
                ),
                'label' => 'label.result', 'expanded' => false, 'required' => false
            ));

        /** Data Transformer */
        $builder->get('amountCompany')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('amountBailif')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Demolition::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_demolition';
    }
}
