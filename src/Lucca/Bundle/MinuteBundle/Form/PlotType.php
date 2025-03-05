<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Lucca\Bundle\ParameterBundle\Entity\Town;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, NumberType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Lucca\Bundle\MinuteBundle\Entity\Plot;
use Lucca\Bundle\ParameterBundle\Repository\TownRepository;

class PlotType extends AbstractType
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parcel', TextType::class, array('label' => 'label.parcel', 'attr' => array(
                'placeholder' => 'help.minute.parcel'
            )))
            ->add('address', TextType::class, array('label' => 'label.address', 'required' => false,
                'attr' => array('placeholder' => 'help.minute.address')
            ))
            ->add('place', TextType::class, array('label' => 'label.place', 'required' => false, 'attr' => array(
                'placeholder' => 'help.minute.place'
            )))
            ->add('isRiskZone', ChoiceType::class, array(
                'label' => 'label.isRiskZone', 'choices' => array(
                    'choice.enabled.yes' => true, 'choice.enabled.no' => false, 'choice.enabled.dont_know' => null
                ), 'required' => true, 'expanded' => true, 'multiple' => false
            ))
            ->add('risk', ChoiceType::class, array(
                'choices' => array(
                    Plot::RISK_FLOOD => Plot::RISK_FLOOD,
                    Plot::RISK_AVALANCHE => Plot::RISK_AVALANCHE,
                    Plot::RISK_FIRE => Plot::RISK_FIRE,
                    Plot::RISK_GROUND_MOVEMENT => Plot::RISK_GROUND_MOVEMENT,
                    Plot::RISK_TECHNOLOGICAL => Plot::RISK_TECHNOLOGICAL,
                    Plot::RISK_OTHER => Plot::RISK_OTHER,
                ), 'label' => 'label.risk', 'expanded' => false, 'required' => false
            ))
            ->add('locationFrom', ChoiceType::class, array(
                'choices' => array(
                    Plot::LOCATION_FROM_ADDRESS => Plot::LOCATION_FROM_ADDRESS,
                    Plot::LOCATION_FROM_COORDINATES => Plot::LOCATION_FROM_COORDINATES,
                    Plot::LOCATION_FROM_MANUAL => Plot::LOCATION_FROM_MANUAL,
                ), 'label' => 'label.locationFrom', 'expanded' => false, 'required' => true
            ))
            ->add('latitude', NumberType::class, array('label' => 'label.latitude', 'required' => false))
            ->add('longitude', NumberType::class, array('label' => 'label.longitude', 'required' => false));


        /** Check if admin - or not */
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('town', EntityType::class, array(
                    'class' => Town::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select'),
                    'multiple' => false, 'expanded' => false, 'label' => 'label.town', 'required' => true,
                    'query_builder' => function (TownRepository $er) {
                        return $er->getValuesOrderedByName();
                    }
                ));
        } else {
            $adherent = $options['adherent'];

            $builder
                ->add('town', EntityType::class, array(
                    'class' => Town::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select'),
                    'multiple' => false, 'expanded' => false, 'label' => 'label.town', 'required' => true,
                    'query_builder' => function (TownRepository $er) use ($adherent) {
                        return $er->getTownByAdherent($adherent);
                    }
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('adherent');
        $resolver->setDefaults(array(
            'data_class' => 'Lucca\MinuteBundle\Entity\Plot',
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }
}
