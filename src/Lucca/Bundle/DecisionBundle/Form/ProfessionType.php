<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Profession;
use Lucca\Bundle\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProfessionType
 *
 * @package Lucca\Bundle\DecisionBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class ProfessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name'))
            ->add('company', TextType::class, array('label' => 'label.company'))
            ->add('amountActivity', MoneyType::class, array('label' => 'label.amountActivity'));

        /** Data Transformer */
        $builder->get('amountActivity')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Profession::class,
            'translation_domain' => 'LuccaDecisionBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_profession';
    }
}
