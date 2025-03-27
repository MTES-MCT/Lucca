<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;
use Lucca\Bundle\DecisionBundle\Entity\Decision;
use Lucca\Bundle\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;
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

class DecisionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /** Tribunal */
            ->add('tribunal', EntityType::class, [
                'class' => Tribunal::class, 'choice_label' => 'name', 'label' => 'label.jurisdiction',
                'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
            ])
            ->add('tribunalCommission', CommissionType::class, [
                'label' => 'label.tribunalCommission', 'required' => false
            ])
            /** Appeal */
            ->add('appeal', ChoiceType::class, [
                'label' => 'label.appeal', 'choices' => [
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false
                ], 'required' => true, 'expanded' => true, 'multiple' => false,
            ])
            ->add('appealCommission', CommissionType::class, [
                'label' => 'label.appealCommission', 'required' => false
            ])
            /** Cassation */
            ->add('cassationComplaint', ChoiceType::class, [
                'label' => 'label.cassationComplaint', 'choices' => [
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false, 'choice.enabled.dont_know' => null
                ], 'required' => true, 'expanded' => true, 'multiple' => false
            ])
            ->add('dateAskCassation', HtmlDateType::class, [
                'label' => 'label.dateAskCassation', 'required' => false
            ])
            ->add('dateAnswerCassation', HtmlDateType::class, [
                'label' => 'label.dateAnswerCassation', 'required' => false
            ])
            ->add('statusCassation', ChoiceType::class, [
                'label' => 'label.statusCassation', 'choices' => [
                    'choice.admission.yes' => true, 'choice.admission.no' => false, 'choice.enabled.dont_know' => null
                ], 'required' => true, 'expanded' => true, 'multiple' => false
            ])
            ->add('cassationComission', CommissionType::class, [
                'label' => 'label.cassationComission', 'required' => false
            ])
            ->add('nameNewCassation', TextType::class, ['label' => 'label.nameNewCassation', 'required' => false])
            /** Europe */
            ->add('dateReferralEurope', HtmlDateType::class, [
                'label' => 'label.dateReferralEurope', 'required' => false
            ])
            ->add('answerEurope', HtmlDateType::class, [
                'label' => 'label.answerEurope', 'required' => false
            ])
            ->add('dataEurope', TextareaType::class, ['label' => 'label.dataEurope', 'required' => false,
                'attr' => ['class' => 'summernote']])
            /** Penalty */
            ->add('amountPenaltyDaily', MoneyType::class, ['label' => 'label.amountPenaltyDaily', 'required' => false])
            ->add('dateStartRecovery', HtmlDateType::class, [
                'label' => 'label.dateStartRecovery', 'required' => false
            ])
            ->add('dateNoticeDdtm', HtmlDateType::class, [
                'label' => 'label.dateNoticeDdtm', 'required' => false
            ])
            ->add('penalties', CollectionType::class, [
                'label' => false, 'entry_type' => PenaltyType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => ['attr' => ['class' => 'penalty']],
                'attr' => ['class' => 'table penalty-collection']
            ])
            ->add('totalPenaltyRecovery', MoneyType::class, ['label' => 'label.totalPenaltyRecovery', 'required' => false,])
            ->add('liquidations', CollectionType::class, [
                'label' => false, 'entry_type' => LiquidationType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => ['attr' => ['class' => 'liquidation']],
                'attr' => ['class' => 'table liquidation-collection']
            ])
            /** Appeal Penalty */
            ->add('appealPenalties', CollectionType::class, [
                'label' => false, 'entry_type' => PenaltyAppealType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => ['attr' => ['class' => 'appeal-penalty']],
                'attr' => ['class' => 'table appeal-penalty-collection']
            ])
            /** Others */
            ->add('contradictories', CollectionType::class, [
                'label' => false, 'entry_type' => ContradictoryType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => ['attr' => ['class' => 'contradictory']],
                'attr' => ['class' => 'table contradictory-collection']
            ])
            ->add('expulsion', ExpulsionType::class, [
                'label' => 'label.expulsion', 'required' => false,
            ])
            ->add('demolition', DemolitionType::class, [
                'label' => 'label.demolition', 'required' => false,
            ]);

        /** Data Transformer */
        $builder->get('amountPenaltyDaily')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('totalPenaltyRecovery')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Decision::class,
            'translation_domain' => 'DecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_decision';
    }
}
