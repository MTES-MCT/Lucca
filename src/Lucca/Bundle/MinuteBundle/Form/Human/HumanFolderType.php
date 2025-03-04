<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Human;

use Lucca\Bundle\MinuteBundle\Entity\Human;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HumanFolderType
 *
 * @package Lucca\Bundle\MinuteBundle\Form\Human
 * @author Terence <terence@numeric-wave.tech>
 */
class HumanFolderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name'))
            ->add('firstname', TextType::class, array('label' => 'label.firstname'))
            ->add('gender', ChoiceType::class, array(
                'choices' => array(
                    Human::GENDER_MALE => Human::GENDER_MALE,
                    Human::GENDER_FEMALE => Human::GENDER_FEMALE,
                ),
                'label' => 'label.gender', 'expanded' => false, 'multiple' => false
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    Human::STATUS_OWNER => Human::STATUS_OWNER,
                    Human::STATUS_UNDIVIDED => Human::STATUS_UNDIVIDED,
                    Human::STATUS_USUFRUCT => Human::STATUS_USUFRUCT,
                    Human::STATUS_OCCUPANT => Human::STATUS_OCCUPANT,
//                    Human::STATUS_NEIGHBOUR => Human::STATUS_NEIGHBOUR,
                    Human::STATUS_OTHER => Human::STATUS_OTHER,
                    Human::STATUS_DIRECTOR => Human::STATUS_DIRECTOR,
                    Human::STATUS_LEADER => Human::STATUS_LEADER,
                    Human::STATUS_MANAGER => Human::STATUS_MANAGER,
                    Human::STATUS_PRESIDENT => Human::STATUS_PRESIDENT,
                ),
                'label' => 'label.status', 'expanded' => false, 'multiple' => false
            ))
            ->add('person', ChoiceType::class, array(
                'choices' => array(
                    Human::PERSON_PHYSICAL => Human::PERSON_PHYSICAL,
                    Human::PERSON_CORPORATION => Human::PERSON_CORPORATION,
                ),
                'label' => 'label.person', 'expanded' => true, 'multiple' => false
            ))
            ->add('company', TextType::class, array('label' => 'label.company', 'attr' => array('class' => 'field-company')))
            ->add('address', TextType::class, array('label' => 'label.addressHuman'))
            ->add('addressCompany', TextType::class, array('label' => 'label.addressCompany'))
            ->add('statusCompany', ChoiceType::class, array(
                'choices' => array(
                    Human::STATUS_OWNER => Human::STATUS_OWNER,
                    Human::STATUS_UNDIVIDED => Human::STATUS_UNDIVIDED,
                    Human::STATUS_USUFRUCT => Human::STATUS_USUFRUCT,
                    Human::STATUS_OCCUPANT => Human::STATUS_OCCUPANT,
                    Human::STATUS_OTHER => Human::STATUS_OTHER,
                    Human::STATUS_DIRECTOR => Human::STATUS_DIRECTOR,
                    Human::STATUS_LEADER => Human::STATUS_LEADER,
                    Human::STATUS_MANAGER => Human::STATUS_MANAGER,
                    Human::STATUS_PRESIDENT => Human::STATUS_PRESIDENT,
                ),
                'label' => 'label.statusCompany', 'expanded' => false, 'multiple' => false, 'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lucca\MinuteBundle\Entity\Human',
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minuteBundle_human';
    }
}
