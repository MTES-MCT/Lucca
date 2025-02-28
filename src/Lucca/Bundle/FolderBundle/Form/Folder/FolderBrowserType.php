<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form\Folder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\AdherentBundle\Repository\AdherentRepository;
use Lucca\ParameterBundle\Entity\Town;
use Lucca\ParameterBundle\Repository\TownRepository;
use Lucca\ParameterBundle\Entity\Intercommunal;
use Lucca\ParameterBundle\Repository\IntercommunalRepository;
use Lucca\ParameterBundle\Entity\Service;

use Lucca\ParameterBundle\Repository\ServiceRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class FolderBrowserType
 *
 * @package Lucca\MinuteBundle\Form\Folder
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class FolderBrowserType extends AbstractType
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * FolderBrowserType constructor.
     *
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', DateTimeType::class, array(
                'label' => 'label.dateStart',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime',
                'required' => false,
                'attr' => array(
                    'class' => 'date-picker'
                )
            ))
            ->add('dateEnd', DateTimeType::class, array(
                'label' => 'label.dateEnd',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime',
                'required' => false,
                'attr' => array(
                    'class' => 'date-picker'
                )
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
                    'expanded' => false,
                    'label' => 'label.adherent',
                    'attr' => array('class' => 'chosen-select'),
                    'query_builder' => function (AdherentRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ))
                ->add('town', EntityType::class, array(
                    'class' => Town::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'label' => 'label.town',
                    'attr' => array('class' => 'chosen-select'),
                    'query_builder' => function (TownRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ))
                ->add('intercommunal', EntityType::class, array(
                    'class' => Intercommunal::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'label' => 'label.intercommunal',
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
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'translation_domain' => 'LuccaMinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_folder';
    }
}