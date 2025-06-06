<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, CollectionType, DateType, TextType, TimeType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\AdherentBundle\Entity\Agent;
use Lucca\Bundle\AdherentBundle\Repository\AgentRepository;
use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Human};

class UpdatingControlType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('dateControl', HtmlDateType::class, array(
                'label' => 'label.dateControl', 'required' => false
            ))
            ->add('hourControl', TimeType::class, array(
                'label' => 'label.hourControl',
                'widget' => 'single_text', 'input' => 'datetime', 'required' => false
            ))
            ->add('datePostal', HtmlDateType::class, array(
                'label' => 'label.datePostal', 'required' => false
            ))
            ->add('dateSended', HtmlDateType::class, array(
                'label' => 'label.dateSended', 'required' => false
            ))
            ->add('dateNotified', HtmlDateType::class, array(
                'label' => 'label.dateNotified', 'required' => false
            ))
            ->add('dateReturned', HtmlDateType::class, array(
                'label' => 'label.dateReturned', 'required' => false
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
            ->add('dateContact', HtmlDateType::class, array(
                'label' => 'label.dateContact', 'required' => false
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
        if ($options['human']) {
            $choicesHuman = array_merge($options['minute']->getHumans()->toArray(), $options['human']->toArray());
        }

        $builder->add('humansByMinute', EntityType::class, array(
            'class' => Human::class, 'label' => false, 'required' => false,
            'multiple' => true, 'choices' => $choicesHuman, 'autocomplete' => true,
            'attr' => array(
                'class' => 'tom-select',
                'data-placeholder' => $this->translator->trans('help.data.select', array(), 'MinuteBundle')
            ),
            'choice_label' => function (Human $human) {
                return $human->getOfficialName() . ' (' . $this->translator->trans($human->getStatus(), array(), 'MinuteBundle') . ')';
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
                'class' => Agent::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'tom-select required'),
                'label' => 'label.agent', 'required' => false, 'mapped' => false, 'autocomplete' => true,
                'query_builder' => function (AgentRepository $er) use ($adherent) {
                    return $er->getAllByAdherent($adherent);
                }
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('minute');
        $resolver->setDefaults(array(
            'data_class' => Control::class,
            'translation_domain' => 'MinuteBundle',
            'human' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_minute_control';
    }
}
