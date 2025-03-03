<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Commission;
use Lucca\Bundle\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommissionType
 *
 * @package Lucca\Bundle\DecisionBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class CommissionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHearing', DateType::class, array(
                'label' => 'label.dateHearing', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('dateAdjournment', DateType::class, array(
                'label' => 'label.dateAdjournment', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('dateDeliberation', DateType::class, array(
                'label' => 'label.dateDeliberation', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('amountFine', MoneyType::class, array('label' => 'label.amountFine'))
            ->add('dateJudicialDesision', DateType::class, array(
                'label' => 'label.dateJudicialDesision', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('statusDecision', ChoiceType::class, array(
                'choices' => array(
                    Commission::STATUS_RELAXED => Commission::STATUS_RELAXED,
                    Commission::STATUS_GUILTY => Commission::STATUS_GUILTY,
                    Commission::STATUS_GUILTY_EXEMPT => Commission::STATUS_GUILTY_EXEMPT,
                    Commission::STATUS_GUILTY_RESTITUTION => Commission::STATUS_GUILTY_RESTITUTION,
                ),
                'label' => 'label.statusDecision', 'expanded' => true
            ))
            ->add('amountPenalty', MoneyType::class, array('label' => 'label.amountPenalty'))
            ->add('dateExecution', DateType::class, array(
                'label' => 'label.dateExecution', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('restitution', TextareaType::class, array('label' => 'label.restitution',
                'attr' => array('class' => 'summernote')));

        /** Data Transformer */
        $builder->get('amountFine')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('amountPenalty')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Commission::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_commission';
    }
}
