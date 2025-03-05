<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Lucca\Bundle\MinuteBundle\Entity\Updating;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nature', ChoiceType::class, array(
                'choices' => array(
                    Updating::NATURE_REGULARIZED => Updating::NATURE_REGULARIZED,
                    Updating::NATURE_AGGRAVATED => Updating::NATURE_AGGRAVATED,
                    Updating::NATURE_UNCHANGED => Updating::NATURE_UNCHANGED,
                ),
                'label' => 'label.nature_updating', 'expanded' => true, 'required' => true
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'label.description', 'attr' => array('class' => 'summernote'), 'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Updating::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'human' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_updating';
    }
}
