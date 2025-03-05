<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, CollectionType, DateType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Agent;
use Lucca\Bundle\AdherentBundle\Repository\AgentRepository;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;
use Lucca\Bundle\ParameterBundle\Repository\TribunalRepository;

class MinuteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** Take options Adherent entity to filter Agent */
        $adherent = $options['adherent'];

        $builder
            ->add('plot', PlotType::class, array(
                'adherent' => $options['adherent'], 'required' => true,
            ))
            ->add('humans', CollectionType::class, [
                'label' => 'label.humansResponsible', 'entry_type' => MinuteHumanType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => ['attr' => ['class' => 'human']],
                'attr' => ['class' => 'table human-collection'],
            ])
            ->add('dateComplaint', DateType::class, array(
                'label' => 'label.dateComplaint', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false,
            ))
            ->add('nameComplaint', TextType::class, array('label' => 'label.nameComplaint', 'required' => false))
            ->add('agent', EntityType::class, array(
                'class' => Agent::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select required'),
                'multiple' => false, 'expanded' => false, 'label' => 'label.agent', 'required' => true,
                'query_builder' => function (AgentRepository $er) use ($adherent) {
                    return $er->getAllActiveByAdherent($adherent);
                }
            ))
            ->add('tribunal', EntityType::class, array(
                'class' => Tribunal::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select required'),
                'multiple' => false, 'expanded' => false, 'label' => 'label.tribunal', 'required' => true,
                'query_builder' => function (TribunalRepository $er) {
                    return $er->getValuesActive();
                }
            ))
            ->add('tribunalCompetent', EntityType::class, array(
                'class' => Tribunal::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select'),
                'multiple' => false, 'expanded' => false, 'label' => 'label.tribunalCompetent', 'required' => false,
                'query_builder' => function (TribunalRepository $er) {
                    return $er->getValuesActive();
                }
            ))
            ->add('origin', ChoiceType::class, array(
                'choices' => array(
                    Minute::ORIGIN_COURIER => Minute::ORIGIN_COURIER,
                    Minute::ORIGIN_PHONE => Minute::ORIGIN_PHONE,
                    Minute::ORIGIN_EAGLE => Minute::ORIGIN_EAGLE,
                    Minute::ORIGIN_AGENT => Minute::ORIGIN_AGENT,
                    Minute::ORIGIN_OTHER => Minute::ORIGIN_OTHER,
                ),
                'label' => 'label.origin', 'expanded' => false, 'required' => true
            ))
            ->add('reporting', TextareaType::class, array('label' => 'label.reporting', 'required' => false,));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('adherent');
        $resolver->setDefaults(array(
            'data_class' => 'Lucca\MinuteBundle\Entity\Minute',
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_minute';
    }
}
