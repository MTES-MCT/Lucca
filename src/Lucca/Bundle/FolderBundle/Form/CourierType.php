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

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Courier;
use Lucca\CoreBundle\Form\DataTransformer\NumberToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourierType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('context', TextareaType::class, array('label' => 'label.context',
                'attr' => array('class' => 'summernote')))
            ->add('civilParty', ChoiceType::class, array(
                'label' => 'label.civilParty', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false
                ), 'required' => true, 'expanded' => true, 'multiple' => false
            ))
            ->add('amount', MoneyType::class, array('label' => 'label.amount'));

        /** Data Transformer */
        $builder->get('amount')->addModelTransformer(new NumberToIntTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Courier::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_courier';
    }
}
