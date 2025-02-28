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

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human;
use Lucca\AdherentBundle\Entity\Agent;
use Lucca\AdherentBundle\Repository\AgentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UpdatingControlType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingControlType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stateControl', ChoiceType::class, array(
                'choices' => array(
                    Control::STATE_INSIDE => Control::STATE_INSIDE,
                    Control::STATE_INSIDE_WITHOUT_CONVOCATION => Control::STATE_INSIDE_WITHOUT_CONVOCATION,
                    Control::STATE_OUTSIDE => Control::STATE_OUTSIDE,
                    Control::STATE_NEIGHBOUR => Control::STATE_NEIGHBOUR,
                ),
                'label' => 'label.stateControl', 'expanded' => true, 'required' => true
            ))
            ->add('dateControl', DateType::class, array(
                'label' => 'label.dateControl', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('hourControl', TimeType::class, array(
                'label' => 'label.hourControl',
                'widget' => 'single_text', 'input' => 'datetime', 'required' => false
            ))
            ->add('datePostal', DateType::class, array(
                'label' => 'label.datePostal', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateSended', DateType::class, array(
                'label' => 'label.dateSended', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateNotified', DateType::class, array(
                'label' => 'label.dateNotified', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('dateReturned', DateType::class, array(
                'label' => 'label.dateReturned', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('reason', ChoiceType::class, array(
                'choices' => array(
                    Control::REASON_ERROR_ADRESS => Control::REASON_ERROR_ADRESS,
                    Control::REASON_UNKNOW_ADRESS => Control::REASON_UNKNOW_ADRESS,
                    Control::REASON_REFUSED_LETTER => Control::REASON_REFUSED_LETTER,
                    Control::REASON_UNCLAIMED_LETTER => Control::REASON_UNCLAIMED_LETTER,
                ),
                'label' => 'label.reason', 'expanded' => false, 'required' => false
            ))
            ->add('dateContact', DateType::class, array(
                'label' => 'label.dateContact', 'attr' => array('class' => 'date-picker'),
                'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input' => 'datetime', 'required' => false
            ))
            ->add('accepted', ChoiceType::class, array(
                'choices' => array(
                    Control::ACCEPTED_OK => Control::ACCEPTED_OK,
                    Control::ACCEPTED_NOK => Control::ACCEPTED_NOK,
                    Control::ACCEPTED_NONE => Control::ACCEPTED_NONE,
                ),
                'label' => 'label.accepted', 'expanded' => false, 'required' => false
            ))
            ->add('summoned', ChoiceType::class, array(
                'label' => 'label.summoned', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false
                ), 'required' => true, 'expanded' => true, 'multiple' => false
            ))
            ->add('agentAttendants', CollectionType::class, array(
                'label' => 'label.agentAttendant', 'entry_type' => AgentAttendantType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => array('attr' => array('class' => 'agentsAttendants')),
                'attr' => array('class' => 'table agentsAttendants-collection'),
            ))
            ->add('courierDelivery', TextType::class, array('label' => 'label.courierDelivery', 'required' => false,
                'attr' => array('placeholder' => 'help.control.courierDelivery')
            ));

        /**
         * Part human - Create a new one or select existant
         */
        $choicesHuman = $options['minute']->getHumans();
        if ($options['human'])
            $choicesHuman = array_merge($options['minute']->getHumans()->toArray(), $options['human']->toArray());

        $builder->add('humansByMinute', EntityType::class, array(
            'class' => 'LuccaMinuteBundle:Human', 'label' => false, 'required' => false,
            'multiple' => true, 'expanded' => false, 'choices' => $choicesHuman,
            'attr' => array(
                'class' => 'select2',
                'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaMinuteBundle')
            ),
            'choice_label' => function (Human $human) {
                return $human->getOfficialName() . ' (' . $this->translator->trans($human->getStatus(), array(), 'LuccaMinuteBundle') . ')';
            },
        ));

        $builder->add('humansByControl', CollectionType::class, array(
            'label' => false, 'entry_type' => HumanType::class,
            'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
            'prototype' => true, 'required' => false, 'by_reference' => true,
            'entry_options' => array('attr' => array('class' => 'human')),
            'attr' => array('class' => 'table human-collection'),
        ));

        /**
         * Part Agent - Create a new one
         */
        $adherent = $options['minute']->getAdherent();

        $builder
            ->add('sameAgent', ChoiceType::class, array(
                'label' => 'label.sameAgent', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false,
                ), 'required' => true, 'expanded' => true, 'multiple' => false, 'mapped' => false
            ))
            ->add('agent', EntityType::class, array(
                'class' => Agent::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select required'),
                'multiple' => false, 'expanded' => false, 'label' => 'label.agent', 'required' => false, 'mapped' => false,
                'query_builder' => function (AgentRepository $er) use ($adherent) {
                    return $er->getAllByAdherent($adherent);
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('minute');
        $resolver->setDefaults(array(
            'data_class' => Control::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'human' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_minute_control';
    }
}
