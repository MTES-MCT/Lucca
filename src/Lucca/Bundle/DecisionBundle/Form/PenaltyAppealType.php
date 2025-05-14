<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\PenaltyAppeal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;

class PenaltyAppealType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('juridiction', TextType::class, ['label' => 'label.juridiction', 'attr' => ['required' => 'required']])
            ->add('dateDecision', HtmlDateType::class, [
                'label' => 'label.dateDecision', 'required' => true
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
            'translation_domain' => 'DecisionBundle',
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
