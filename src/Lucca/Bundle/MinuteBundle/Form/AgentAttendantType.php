<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AgentAttendantType
 *
 * @package Lucca\Bundle\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class AgentAttendantType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name', 'required' => true))
            ->add('firstname', TextType::class, array('label' => 'label.firstname', 'required' => true))
            ->add('function', TextType::class, array('label' => 'label.function', 'required' => true));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lucca\MinuteBundle\Entity\AgentAttendant',
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minuteBundle_agent_attendant';
    }
}
