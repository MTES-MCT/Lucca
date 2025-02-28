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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Closure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClosureType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class ClosureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    Closure::STATUS_REGULARIZED => Closure::STATUS_REGULARIZED,
                    Closure::STATUS_EXEC_OFFICE => Closure::STATUS_EXEC_OFFICE,
                    Closure::STATUS_RELAXED => Closure::STATUS_RELAXED,
                    Closure::STATUS_OTHER => Closure::STATUS_OTHER,
                ),
                'label' => 'label.status', 'expanded' => true, 'required' => true
            ))
            ->add('dateClosing', DateType::class, array(
                'label' => 'label.dateClosing', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => true
            ))
            ->add('natureRegularized', ChoiceType::class, array(
                'choices' => array(
                    Closure::NATURE_REGULARIZED_ADMINISTRATIVE => Closure::NATURE_REGULARIZED_ADMINISTRATIVE,
                    Closure::NATURE_REGULARIZED_FIELD => Closure::NATURE_REGULARIZED_FIELD,
                ),
                'label' => 'label.natureRegularized', 'expanded' => true, 'required' => false,
                'placeholder' => false
            ))
            ->add('initiatingStructure', ChoiceType::class, array(
                'choices' => array(
                    Closure::INITSTRUCT_DDTM => Closure::INITSTRUCT_DDTM,
                    Closure::INITSTRUCT_DDT => Closure::INITSTRUCT_DDT,
                    Closure::INITSTRUCT_TOWN => Closure::INITSTRUCT_TOWN,
                    Closure::INITSTRUCT_OTHER => Closure::INITSTRUCT_OTHER,
                ),
                'label' => 'label.initiatingStructure', 'expanded' => true, 'required' => false,
                'placeholder' => false
            ))
            ->add('reason', TextType::class, array('label' => 'label.reasonClosing', 'required' => false))
            ->add('observation', TextareaType::class, array('label' => 'label.observation', 'required' => false,
                'attr' => array('class' => 'summernote')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Closure::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_closure';
    }
}
