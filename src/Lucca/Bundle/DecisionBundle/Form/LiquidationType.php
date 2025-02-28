<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form;

use Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity\Liquidation;
use Lucca\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LiquidationType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class LiquidationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('amountPenalty', MoneyType::class, array('label' => 'label.amountPenalty'))
            ->add('dateStart', DateType::class, array(
                'label' => 'label.dateStart', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('dateEnd', DateType::class, array(
                'label' => 'label.dateEnd', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ));

        /** Data Transformer */
        $builder->get('amountPenalty')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Liquidation::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_liquidation';
    }
}
