<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form\Courier;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\CourierHumanEdition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourierOffenderEditionType
 *
 * @package Lucca\MinuteBundle\Form\Courier
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierOffenderEditionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('letterOffenderEdited', CheckboxType::class, array('label' => 'label.letterOffenderEdited', 'required' => false))
            ->add('letterOffender', TextareaType::class, array('label' => 'label.letterOffender', 'required' => true,
                'attr' => array('class' => 'summernote-letter')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CourierHumanEdition::class,
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_courierEdition_offender';
    }
}
