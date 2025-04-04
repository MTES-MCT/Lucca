<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
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

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;

class CommissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHearing', HtmlDateType::class, [
                'label' => 'label.dateHearing', 'required' => true
            ])
            ->add('dateAdjournment', HtmlDateType::class, [
                'label' => 'label.dateAdjournment', 'required' => true
            ])
            ->add('dateDeliberation', HtmlDateType::class, [
                'label' => 'label.dateDeliberation', 'required' => true
            ])
            ->add('amountFine', MoneyType::class, ['label' => 'label.amountFine'])
            ->add('dateJudicialDesision', HtmlDateType::class, [
                'label' => 'label.dateJudicialDesision', 'required' => true
            ])
            ->add('statusDecision', ChoiceType::class, [
                'choices' => [
                    Commission::STATUS_RELAXED => Commission::STATUS_RELAXED,
                    Commission::STATUS_GUILTY => Commission::STATUS_GUILTY,
                    Commission::STATUS_GUILTY_EXEMPT => Commission::STATUS_GUILTY_EXEMPT,
                    Commission::STATUS_GUILTY_RESTITUTION => Commission::STATUS_GUILTY_RESTITUTION,
                ],
                'label' => 'label.statusDecision', 'expanded' => true
            ])
            ->add('amountPenalty', MoneyType::class, ['label' => 'label.amountPenalty'])
            ->add('dateExecution', HtmlDateType::class, [
                'label' => 'label.dateExecution', 'required' => true
            ])
            ->add('restitution', TextareaType::class, ['label' => 'label.restitution',
                'attr' => ['class' => 'summernote']]);

        /** Data Transformer */
        $builder->get('amountFine')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('amountPenalty')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commission::class,
            'translation_domain' => 'DecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_commission';
    }
}
