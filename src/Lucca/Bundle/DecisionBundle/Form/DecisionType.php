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

use Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Decision;
use Lucca\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Lucca\ParameterBundle\Entity\Tribunal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
 * Class DecisionType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class DecisionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /** Tribunal */
            ->add('tribunal', EntityType::class, array(
                'class' => Tribunal::class, 'choice_label' => 'name', 'label' => 'label.jurisdiction', 'required' => true,
                'multiple' => false, 'expanded' => false, 'attr' => array('class' => 'chosen-select')
            ))
            ->add('tribunalCommission', CommissionType::class, array(
                'label' => 'label.tribunalCommission', 'required' => false
            ))
            /** Appeal */
            ->add('appeal', ChoiceType::class, array(
                'label' => 'label.appeal', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false
                ), 'required' => true, 'expanded' => true, 'multiple' => false,
            ))
            ->add('appealCommission', CommissionType::class, array(
                'label' => 'label.appealCommission', 'required' => false
            ))
            /** Cassation */
            ->add('cassationComplaint', ChoiceType::class, array(
                'label' => 'label.cassationComplaint', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false, 'choice.enabled.dont_know' => null
                ), 'required' => true, 'expanded' => true, 'multiple' => false
            ))
            ->add('dateAskCassation', DateType::class, array(
                'label' => 'label.dateAskCassation', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateAnswerCassation', DateType::class, array(
                'label' => 'label.dateAnswerCassation', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('statusCassation', ChoiceType::class, array(
                'label' => 'label.statusCassation', 'choices' => array(
                    'choice.admission.yes' => true, 'choice.admission.no' => false, 'choice.enabled.dont_know' => null
                ), 'required' => true, 'expanded' => true, 'multiple' => false
            ))
            ->add('cassationComission', CommissionType::class, array(
                'label' => 'label.cassationComission', 'required' => false
            ))
            ->add('nameNewCassation', TextType::class, array('label' => 'label.nameNewCassation', 'required' => false))
            /** Europe */
            ->add('dateReferralEurope', DateType::class, array(
                'label' => 'label.dateReferralEurope', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('answerEurope', DateType::class, array(
                'label' => 'label.answerEurope', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dataEurope', TextareaType::class, array('label' => 'label.dataEurope', 'required' => false,
                'attr' => array('class' => 'summernote')))
            /** Penalty */
            ->add('amountPenaltyDaily', MoneyType::class, array('label' => 'label.amountPenaltyDaily', 'required' => false))
            ->add('dateStartRecovery', DateType::class, array(
                'label' => 'label.dateStartRecovery', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateNoticeDdtm', DateType::class, array(
                'label' => 'label.dateNoticeDdtm', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('penalties', CollectionType::class, array(
                'label' => false, 'entry_type' => PenaltyType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => array('attr' => array('class' => 'penalty')),
                'attr' => array('class' => 'table penalty-collection')
            ))
            ->add('totalPenaltyRecovery', MoneyType::class, array('label' => 'label.totalPenaltyRecovery', 'required' => false,))
            ->add('liquidations', CollectionType::class, array(
                'label' => false, 'entry_type' => LiquidationType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => array('attr' => array('class' => 'liquidation')),
                'attr' => array('class' => 'table liquidation-collection')
            ))
            /** Appeal Penalty */
            ->add('appealPenalties', CollectionType::class, array(
                'label' => false, 'entry_type' => PenaltyAppealType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => array('attr' => array('class' => 'appeal-penalty')),
                'attr' => array('class' => 'table appeal-penalty-collection')
            ))
            /** Others */
            ->add('contradictories', CollectionType::class, array(
                'label' => false, 'entry_type' => ContradictoryType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => array('attr' => array('class' => 'contradictory')),
                'attr' => array('class' => 'table contradictory-collection')
            ))
            ->add('expulsion', ExpulsionType::class, array(
                'label' => 'label.expulsion', 'required' => false,
            ))
            ->add('demolition', DemolitionType::class, array(
                'label' => 'label.demolition', 'required' => false,
            ));

        /** Data Transformer */
        $builder->get('amountPenaltyDaily')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('totalPenaltyRecovery')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Decision::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_decision';
    }
}
