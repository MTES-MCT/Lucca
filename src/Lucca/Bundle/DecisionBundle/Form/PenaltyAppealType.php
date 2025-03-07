<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\PenaltyAppeal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PenaltyAppealType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('juridiction', TextType::class, ['label' => 'label.juridiction'])
            ->add('dateDecision', DateType::class, [
                'label' => 'label.dateDecision', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('kindDecision', TextType::class, ['label' => 'label.kindDecision']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PenaltyAppeal::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_penalty_appeal';
    }
}
