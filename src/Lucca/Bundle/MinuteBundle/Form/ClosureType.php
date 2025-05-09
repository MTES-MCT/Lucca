<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;
use Lucca\Bundle\MinuteBundle\Entity\Closure;

class ClosureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('dateClosing', HtmlDateType::class, [
                'label' => 'label.dateClosing', 'required' => true
            ])
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Closure::class,
            'translation_domain' => 'MinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_closure';
    }
}
