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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Updating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UpdatingType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_updating';
    }
}
