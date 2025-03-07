<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextareaType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MinuteBundle\Entity\Human;

class HumanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                    Human::STATUS_NEIGHBOUR => Human::STATUS_NEIGHBOUR,
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
            ->add('address', TextareaType::class, array('label' => 'label.addressHuman'))
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Human::class,
            'translation_domain' => 'MinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_human';
    }
}
