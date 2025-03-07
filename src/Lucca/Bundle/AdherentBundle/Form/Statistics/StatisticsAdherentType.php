<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Form\Statistics;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal, Service};
use Lucca\Bundle\ParameterBundle\Repository\{IntercommunalRepository, ServiceRepository, TownRepository};

class StatisticsAdherentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adherent', EntityType::class, array(
                'class' => Adherent::class, 'choice_label' => 'officialName', 'required' => false,
                'multiple' => true, 'expanded' => false, 'label' => 'label.adherent',
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'mapped' => false,
            'translation_domain' => 'AdherentBundle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_adherentbundle_browser_adherent';
    }
}
