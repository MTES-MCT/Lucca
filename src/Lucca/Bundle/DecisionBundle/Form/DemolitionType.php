<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Demolition;
use Lucca\Bundle\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemolitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', TextType::class, ['label' => 'label.company_intervening'])
            ->add('amountCompany', MoneyType::class, ['label' => 'label.amountCompany'])
            ->add('dateDemolition', DateType::class, [
                'label' => 'label.dateDemolition', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('bailif', TextType::class, ['label' => 'label.bailif'])
            ->add('amountBailif', MoneyType::class, ['label' => 'label.amountBailif'])
            ->add('professions', CollectionType::class, [
                'label' => false, 'entry_type' => ProfessionType::class, 'prototype' => true,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true, 'required' => false,
                'by_reference' => true, 'entry_options' => ['attr' => ['class' => 'profession']],
                'attr' => ['class' => 'table profession-collection'],
            ])
            ->add('comment', TextareaType::class, ['label' => 'label.comment',
                'attr' => ['class' => 'summernote'], 'required' => false
            ])
            ->add('result', ChoiceType::class, [
                'choices' => [
                    Demolition::RESULT_WAITING => Demolition::RESULT_WAITING,
                    Demolition::RESULT_REALIZED => Demolition::RESULT_REALIZED,
                    Demolition::RESULT_REPORTED => Demolition::RESULT_REPORTED,
                    Demolition::RESULT_CANCELLED => Demolition::RESULT_CANCELLED,
                ],
                'label' => 'label.result', 'expanded' => false, 'required' => false
            ]);

        /** Data Transformer */
        $builder->get('amountCompany')->addModelTransformer(new NumberToIntTransformer());
        $builder->get('amountBailif')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demolition::class,
            'translation_domain' => 'DecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_demolition';
    }
}
