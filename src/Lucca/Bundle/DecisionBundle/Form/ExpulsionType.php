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

/**
 * Class ExpulsionType
 *
 * @package Lucca\Bundle\DecisionBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class ExpulsionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lawFirm', TextType::class, array('label' => 'label.lawFirm'))
            ->add('amountDelivrery', MoneyType::class, array('label' => 'label.amountDelivrery'))
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
            ->add('dateJudicialDesision', DateType::class, array(
                'label' => 'label.dateJudicialDesision', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('statusDecision', TextType::class, array('label' => 'label.statusDecision'))
            ->add('comment', TextareaType::class, array('label' => 'label.comment',
                'attr' => array('class' => 'summernote'), 'required' => false
            ));

        /** Data Transformer */
        $builder->get('amountDelivrery')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Expulsion::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_expulsion';
    }
}
