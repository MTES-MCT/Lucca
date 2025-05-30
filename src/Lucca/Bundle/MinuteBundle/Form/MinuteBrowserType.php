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
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Service, Town};
use Lucca\Bundle\ParameterBundle\Repository\{IntercommunalRepository, ServiceRepository, TownRepository};

// TODO fix name initialization bug
class MinuteBrowserType extends AbstractType
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

        $adherentTowns = $options['adherent_towns'];
        $adherentIntercommunals = $options['adherent_intercommunals'];

        $builder
            ->add('dateStart', HtmlDateType::class, [
                'label' => 'label.dateStart', 'required' => false
            ])
            ->add('dateEnd', HtmlDateType::class, [
                'label' => 'label.dateEnd', 'required' => false
            ])
            ->add('num', TextType::class, array(
                    'label' => 'label.num',
                    'required' => false)
            )
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    Minute::STATUS_OPEN => Minute::STATUS_OPEN,
                    Minute::STATUS_CONTROL => Minute::STATUS_CONTROL,
                    Minute::STATUS_FOLDER => Minute::STATUS_FOLDER,
                    Minute::STATUS_COURIER => Minute::STATUS_COURIER,
                    Minute::STATUS_AIT => Minute::STATUS_AIT,
                    Minute::STATUS_UPDATING => Minute::STATUS_UPDATING,
                    Minute::STATUS_DECISION => Minute::STATUS_DECISION,
                    Minute::STATUS_CLOSURE => Minute::STATUS_CLOSURE,
                ),
                'label' => 'label.statusMinute', 'required' => false, 'autocomplete' => true,
                'multiple' => true, 'attr' => array('class' => 'tom-select'),
                'placeholder' => false
            ));

        if ($adherentTowns === null || (gettype($adherentTowns) === 'array' && count($adherentTowns) > 1)) {
            $builder
                ->add('folder_town', EntityType::class, array(
                    'class' => Town::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.town',
                    'attr' => array('class' => 'tom-select'),
                    'choices' => $adherentTowns,
                    'query_builder' => function (TownRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ));
        }
        if ($adherentIntercommunals === null || (gettype($adherentIntercommunals) === 'array' && count($adherentIntercommunals) > 1)) {
            $builder
                ->add('folder_intercommunal', EntityType::class, array(
                    'class' => Intercommunal::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.intercommunal',
                    'attr' => array('class' => 'tom-select'),
                    'choices' => $adherentIntercommunals,
                    'query_builder' => function (IntercommunalRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ));
        }

        /** Check if admin - or not */
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || $options['allFiltersAvailable']) {
            $builder
                ->add('adherent', EntityType::class, array(
                    'class' => Adherent::class,
                    'choice_label' => 'officialName',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.adherent',
                    'attr' => array('class' => 'tom-select'),
                    'query_builder' => function (AdherentRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ))
                ->add('adherent_intercommunal', EntityType::class, array(
                    'class' => Intercommunal::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'expanded' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.intercommunal',
                    'attr' => array('class' => 'tom-select'),
                    'query_builder' => function (IntercommunalRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ))
                ->add('adherent_town', EntityType::class, array(
                    'class' => Town::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.town',
                    'attr' => array('class' => 'tom-select'),
                    'query_builder' => function (TownRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ))
                ->add('service', EntityType::class, array(
                    'class' => Service::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.service',
                    'attr' => array('class' => 'tom-select'),
                    'query_builder' => function (ServiceRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'mapped' => false,
            'translation_domain' => 'MinuteBundle',
            'adherent_towns' => null,
            'adherent_intercommunals' => null,
            'allFiltersAvailable' => false
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
