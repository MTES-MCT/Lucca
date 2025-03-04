<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Statistics;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Lucca\Bundle\ParameterBundle\Entity\Town;
use Lucca\Bundle\ParameterBundle\Entity\Intercommunal;
use Lucca\Bundle\ParameterBundle\Entity\Service;
use Lucca\Bundle\ParameterBundle\Repository\IntercommunalRepository;
use Lucca\Bundle\ParameterBundle\Repository\ServiceRepository;
use Lucca\Bundle\ParameterBundle\Repository\TownRepository;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;

/**
 * Class BrowserMinuteType
 *
 * @package Lucca\Bundle\MinuteBundle\Form\Statistics
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class StatsGraphMinuteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', DateTimeType::class, array(
                'label' => 'label.dateStartOpening', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker')
            ))
            ->add('dateEnd', DateTimeType::class, array(
                'label' => 'label.dateEndOpening', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'input' => 'datetime', 'attr' => array('class' => 'date-picker')
            ))
            ->add('adherent', EntityType::class, array(
                'class' => Adherent::class, 'choice_label' => 'officialName', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.adherentSimple',
                'attr' => array('class' => 'chosen-select'),
                'query_builder' => function (AdherentRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('town', EntityType::class, array(
                'class' => Town::class, 'choice_label' => 'formLabelInsee', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.town',
                'attr' => array('class' => 'chosen-select'),
                'query_builder' => function (TownRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('townAdherent', EntityType::class, array(
                'class' => Town::class, 'choice_label' => 'formLabelInsee', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.town',
                'attr' => array('class' => 'chosen-select'),
                'query_builder' => function (TownRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('intercommunal', EntityType::class, array(
                'class' => Intercommunal::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.intercommunal',
                'attr' => array('class' => 'chosen-select'),
                'query_builder' => function (IntercommunalRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('service', EntityType::class, array(
                'class' => Service::class, 'choice_label' => 'name', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.service',
                'attr' => array('class' => 'chosen-select'),
                'query_builder' => function (ServiceRepository $repo) {
                    return $repo->getValuesActive();
                }
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'mapped' => false,
            'translation_domain' => 'LuccaMinuteBundle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minuteBundle_browser_minute';
    }
}
