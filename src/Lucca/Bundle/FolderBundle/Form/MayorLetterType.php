<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Form;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\MayorLetter;
use Lucca\AdherentBundle\Entity\Agent;
use Lucca\AdherentBundle\Repository\AgentRepository;
use Lucca\ParameterBundle\Entity\Town;
use Lucca\ParameterBundle\Repository\TownRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MayorLetterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** Take options Adherent entity to filter Agent */
        $adherent = $options['adherent'];

        $builder
            ->add('name', TextType::class, array('label' => 'label.nameMayor', 'required' => true,
                'attr' => array('class' => 'js-nameMayor')))
            ->add('gender', ChoiceType::class, array(
                'choices' => array(
                    MayorLetter::GENDER_MALE => MayorLetter::GENDER_MALE,
                    MayorLetter::GENDER_FEMALE => MayorLetter::GENDER_FEMALE,
                ),
                'label' => 'label.gender', 'expanded' => false, 'multiple' => false,
                'attr' => array('class' => 'js-gender')
            ))
            ->add('address', TextareaType::class, array('label' => 'label.addressMayor',
                'attr' => array('class' => 'js-addressMayor')))
            ->add('dateSended', DateType::class, array(
                'label' => 'label.dateSended', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'input' => 'datetime', 'required' => false,
                'attr' => array('class' => 'date-picker js-dateSended')
            ))
            ->add('town', EntityType::class, array(
                'class' => Town::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label' => 'label.town',
                'required' => false,
                'attr' => array('class' => 'chosen-select js-select-town'),
                'query_builder' => function (TownRepository $repo) {
                    return $repo->getValuesActive();
                }
            ))
            ->add('agent', EntityType::class, array(
                'class' => Agent::class, 'choice_label' => 'formLabel', 'attr' => array('class' => 'chosen-select required'),
                'multiple' => false, 'expanded' => false, 'label' => 'label.followedBy', 'required' => true,
                'query_builder' => function (AgentRepository $er) use ($adherent) {
                    return $er->getAllActiveByAdherent($adherent);
                }
            ))
            ->add('folders', HiddenType::class, array(
                'mapped'=> false,
                'attr' => array('class' => 'js-folders'),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('adherent');
        $resolver->setDefaults(array(
            'data_class' => MayorLetter::class,
            'translation_domain' => 'LuccaMinuteBundle',
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_minutebundle_mayorletter';
    }
}