<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form\Folder;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\ParameterBundle\Entity\Town;
use Lucca\Bundle\ParameterBundle\Repository\TownRepository;
use Lucca\Bundle\ParameterBundle\Entity\Intercommunal;
use Lucca\Bundle\ParameterBundle\Repository\IntercommunalRepository;
use Lucca\Bundle\ParameterBundle\Entity\Service;

use Lucca\Bundle\ParameterBundle\Repository\ServiceRepository;

/**
 * Class FolderBrowserType
 *
 * @package Lucca\Bundle\FolderBundle\Form\Folder
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class FolderBrowserType extends AbstractType
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', DateTimeType::class, array(
                'label' => 'label.dateStart',
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => false
            ))
            ->add('dateEnd', DateTimeType::class, array(
                'label' => 'label.dateEnd',
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => false
            ))
            ->add('num', TextType::class, array(
                    'label' => 'label.num',
                    'required' => false)
            )
            ->add('numFolder', TextType::class, array(
                'label' => 'label.num_folder',
                'required' => false)
            );

        /** Check if admin - or not */
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
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
                ->add('town', EntityType::class, array(
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
                ->add('intercommunal', EntityType::class, array(
                    'class' => Intercommunal::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'autocomplete' => true,
                    'label' => 'label.intercommunal',
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
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'translation_domain' => 'FolderBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_folderBundle_folder';
    }
}
