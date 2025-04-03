<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Statistics;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, DateTimeType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\FolderBundle\Entity\{Folder, Natinf};
use Lucca\Bundle\FolderBundle\Repository\NatinfRepository;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Minute, Plot};
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Service, Town};
use Lucca\Bundle\ParameterBundle\Repository\{IntercommunalRepository, ServiceRepository, TownRepository};

class BrowserMinuteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', DateTimeType::class, array(
                'label' => 'label.dateStartOpening', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker'), 'required' => false
            ))
            ->add('dateEnd', DateTimeType::class, array(
                'label' => 'label.dateEndOpening', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker'), 'required' => false
            ))
            ->add('adherent', EntityType::class, array(
                'class' => Adherent::class, 'choice_label' => 'officialName', 'required' => false,
                'multiple' => true, 'label' => 'label.adherentSimple', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (AdherentRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('town', EntityType::class, array(
                'class' => Town::class, 'choice_label' => 'formLabelInsee', 'required' => false,
                'multiple' => true, 'label' => 'label.town', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (TownRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('townAdherent', EntityType::class, array(
                'class' => Town::class, 'choice_label' => 'formLabelInsee', 'required' => false,
                'multiple' => true, 'label' => 'label.town', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (TownRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('intercommunal', EntityType::class, array(
                'class' => Intercommunal::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'label' => 'label.intercommunal', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (IntercommunalRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('service', EntityType::class, array(
                'class' => Service::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'label' => 'label.service', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (ServiceRepository $repo) {
                    return $repo->getValuesActive();
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
                'attr' => array('class' => 'tom-select'), 'autocomplete' => true,
                'label' => 'label.origin', 'required' => false, 'multiple' => true
            ))
            ->add('risk', ChoiceType::class, array(
                'choices' => array(
                    Plot::RISK_FLOOD => Plot::RISK_FLOOD,
                    Plot::RISK_AVALANCHE => Plot::RISK_AVALANCHE,
                    Plot::RISK_FIRE => Plot::RISK_FIRE,
                    Plot::RISK_GROUND_MOVEMENT => Plot::RISK_GROUND_MOVEMENT,
                    Plot::RISK_TECHNOLOGICAL => Plot::RISK_TECHNOLOGICAL,
                    Plot::RISK_OTHER => Plot::RISK_OTHER,
                ),
                'attr' => array('class' => 'tom-select'),
                'label' => 'label.risk', 'required' => false, 'multiple' => true, 'autocomplete' => true,
            ))
            ->add('stateControl', ChoiceType::class, array(
                'choices' => array(
                    Control::STATE_INSIDE => Control::STATE_INSIDE,
                    Control::STATE_INSIDE_WITHOUT_CONVOCATION => Control::STATE_INSIDE_WITHOUT_CONVOCATION,
                    Control::STATE_NEIGHBOUR => Control::STATE_NEIGHBOUR,
                    Control::STATE_OUTSIDE => Control::STATE_OUTSIDE,
                ),
                'attr' => array('class' => 'tom-select'), 'autocomplete' => true,
                'label' => 'label.stateControl', 'required' => false, 'multiple' => true
            ))
            ->add('nature', ChoiceType::class, array(
                'choices' => array(
                    Folder::NATURE_HUT => Folder::NATURE_HUT,
                    Folder::NATURE_FORMAL_OFFENSE => Folder::NATURE_FORMAL_OFFENSE,
                    Folder::NATURE_SUBSTANTIVE_OFFENSE => Folder::NATURE_SUBSTANTIVE_OFFENSE,
                    Folder::NATURE_OTHER => Folder::NATURE_OTHER,
                    Folder::NATURE_OBSTACLE => Folder::NATURE_OBSTACLE,
                ),
                'attr' => array('class' => 'tom-select'), 'autocomplete' => true,
                'label' => 'label.nature', 'required' => false, 'multiple' => true
            ))
            ->add('natinfs', EntityType::class, array(
                'class' => Natinf::class, 'choice_label' => 'formLabel', 'required' => false,
                'multiple' => true, 'label' => 'label.natinfs', 'autocomplete' => true,
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (NatinfRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('dateStartClosure', DateTimeType::class, array(
                'label' => 'label.dateStartClosure', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker'), 'required' => false
            ))
            ->add('dateEndClosure', DateTimeType::class, array(
                'label' => 'label.dateEndClosure', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker'), 'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'mapped' => false,
            'translation_domain' => 'MinuteBundle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_browser_minute';
    }
}
