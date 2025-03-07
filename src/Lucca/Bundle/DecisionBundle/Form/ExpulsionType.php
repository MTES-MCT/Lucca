<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Expulsion;
use Lucca\Bundle\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpulsionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lawFirm', TextType::class, ['label' => 'label.lawFirm'])
            ->add('amountDelivrery', MoneyType::class, ['label' => 'label.amountDelivrery'])
            ->add('dateHearing', DateType::class, [
                'label' => 'label.dateHearing', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('dateAdjournment', DateType::class, [
                'label' => 'label.dateAdjournment', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('dateDeliberation', DateType::class, [
                'label' => 'label.dateDeliberation', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('dateJudicialDesision', DateType::class, [
                'label' => 'label.dateJudicialDesision', 'attr' => ['class' => 'date-picker'],
                'widget' => 'single_text', 'input' => 'datetime', 'required' => true
            ])
            ->add('statusDecision', TextType::class, ['label' => 'label.statusDecision'])
            ->add('comment', TextareaType::class, ['label' => 'label.comment',
                'attr' => ['class' => 'summernote'], 'required' => false
            ]);

        /** Data Transformer */
        $builder->get('amountDelivrery')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expulsion::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_expulsion';
    }
}
