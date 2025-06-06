<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Agent;
use Lucca\Bundle\ParameterBundle\Entity\Tribunal;

class AgentType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name'))
            ->add('firstname', TextType::class, array('label' => 'label.firstname'))
            ->add('function', ChoiceType::class, array(
                'choices' => array(
                    Agent::FUNCTION_MAYOR => Agent::FUNCTION_MAYOR,
                    Agent::FUNCTION_DEPUTY => Agent::FUNCTION_DEPUTY,
                    Agent::FUNCTION_POLICE => Agent::FUNCTION_POLICE,
                    Agent::FUNCTION_DGS => Agent::FUNCTION_DGS,
                    Agent::FUNCTION_DST => Agent::FUNCTION_DST,
                    Agent::FUNCTION_ASVP => Agent::FUNCTION_ASVP,
                    Agent::FUNCTION_TOWN_MANAGER => Agent::FUNCTION_TOWN_MANAGER,
                    Agent::FUNCTION_ADMIN_AGENT => Agent::FUNCTION_ADMIN_AGENT,
                    Agent::FUNCTION_COUNTRY_GUARD => Agent::FUNCTION_COUNTRY_GUARD,
                    Agent::FUNCTION_COUNTRY_AGENT => Agent::FUNCTION_COUNTRY_AGENT,
                ),
                'label' => 'label.function', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select', 'required' => true),
            ))
            ->add('commission', TextType::class, array('label' => 'label.commission', 'required' => false))
            ->add('tribunal', EntityType::class, array(
                'class' => Tribunal::class, 'choice_label' => 'formLabel',
                'label' => 'label.tribunal', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
            ));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Agent::class,
            'translation_domain' => 'AdherentBundle',
            'required' => true
        ));
    }
}
