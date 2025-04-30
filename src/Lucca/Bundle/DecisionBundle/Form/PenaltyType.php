<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
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

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;

class PenaltyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFolder', HtmlDateType::class, [
                'label' => 'label.dateFolder6', 'required' => true
            ])
            ->add('preparedBy', TextType::class, ['label' => 'label.preparedBy', 'attr' => ['required' => 'required']])
            ->add('nature', ChoiceType::class, [
                'choices' => [
                    Penalty::NATURE_REGULARIZED => Penalty::NATURE_REGULARIZED,
                    Penalty::NATURE_AGGRAVATED => Penalty::NATURE_AGGRAVATED,
                    Penalty::NATURE_UNCHANGED => Penalty::NATURE_UNCHANGED,
                ],
                'label' => 'label.nature', 'expanded' => false, 'multiple' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Penalty::class,
            'translation_domain' => 'DecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_penalty';
    }
}
