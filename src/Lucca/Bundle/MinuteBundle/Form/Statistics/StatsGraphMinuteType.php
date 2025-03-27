<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Statistics;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal, Service};
use Lucca\Bundle\ParameterBundle\Repository\{IntercommunalRepository, ServiceRepository, TownRepository};

class StatsGraphMinuteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', DateTimeType::class, array(
                'label' => 'label.dateStartOpening', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker')
            ))
            ->add('dateEnd', DateTimeType::class, array(
                'label' => 'label.dateEndOpening', 'widget' => 'single_text',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker')
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
                'multiple' => true, 'label' => 'label.service',
                'attr' => array('class' => 'tom-select'),
                'query_builder' => function (ServiceRepository $repo) {
                    return $repo->getValuesActive();
                }
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
