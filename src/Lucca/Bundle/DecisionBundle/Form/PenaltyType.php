<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Penalty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PenaltyType
 *
 * @package Lucca\Bundle\DecisionBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class PenaltyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFolder', DateType::class, array(
                'label' => 'label.dateFolder6', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('preparedBy', TextType::class, array('label' => 'label.preparedBy'))
            ->add('nature', ChoiceType::class, array(
                'choices' => array(
                    Penalty::NATURE_REGULARIZED => Penalty::NATURE_REGULARIZED,
                    Penalty::NATURE_AGGRAVATED => Penalty::NATURE_AGGRAVATED,
                    Penalty::NATURE_UNCHANGED => Penalty::NATURE_UNCHANGED,
                ),
                'label' => 'label.nature', 'expanded' => false, 'multiple' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Penalty::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_penalty';
    }
}
