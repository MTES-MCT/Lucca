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

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human;
use Lucca\MinuteBundle\Form\Human\HumanFolderType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FolderType
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nature', ChoiceType::class, array(
                'choices' => array(
                    Folder::NATURE_HUT => Folder::NATURE_HUT,
                    Folder::NATURE_FORMAL_OFFENSE => Folder::NATURE_FORMAL_OFFENSE,
                    Folder::NATURE_SUBSTANTIVE_OFFENSE => Folder::NATURE_SUBSTANTIVE_OFFENSE,
                    Folder::NATURE_OTHER => Folder::NATURE_OTHER,
                    Folder::NATURE_OBSTACLE => Folder::NATURE_OBSTACLE,
                ), 'label' => 'label.nature', 'expanded' => true, 'required' => true,
                'attr' => array("class" => "js-nature")
            ))
            ->add('reasonObstacle', ChoiceType::class, array(
                'choices' => array(
                    Folder::REASON_OBS_REFUSE_ACCESS_AFTER_LETTER => Folder::REASON_OBS_REFUSE_ACCESS_AFTER_LETTER,
                    Folder::REASON_OBS_REFUSE_BY_RECIPIENT => Folder::REASON_OBS_REFUSE_BY_RECIPIENT,
                    Folder::REASON_OBS_UNCLAIMED_BY_RECIPIENT => Folder::REASON_OBS_UNCLAIMED_BY_RECIPIENT,
                    Folder::REASON_OBS_ACCESS_REFUSED => Folder::REASON_OBS_ACCESS_REFUSED,
                    Folder::REASON_OBS_ABSENT_DURING_CONTROL => Folder::REASON_OBS_ABSENT_DURING_CONTROL,
                ), 'label' => 'label.reasonObstacle', 'expanded' => false, 'required' => false
            ))
            ->add('elements', CollectionType::class, array(
                'label' => 'label.elements', 'entry_type' => ElementType::class,
                'allow_add' => true, 'allow_delete' => false, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => false,
                'entry_options' => array('attr' => array('class' => 'element')),
                'attr' => array('class' => 'element-collection'),
            ));

        $choicesHuman = $options['minute']->getHumans();
        $builder
            ->add('humansByMinute', EntityType::class, array(
                'class' => 'LuccaMinuteBundle:Human', 'label' => false, 'required' => false,
                'multiple' => true, 'expanded' => false, 'choices' => $choicesHuman,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaMinuteBundle')
                ),
                'choice_label' => function (Human $human) {
                    return $human->getOfficialName() . ' (' . $this->translator->trans($human->getStatus(), array(), 'LuccaMinuteBundle') . ')';
                },
            ));

        $builder
            ->add('humansByFolder', CollectionType::class, [
                'label' => false, 'entry_type' => HumanFolderType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => array('attr' => array('class' => 'human')),
                'attr' => array('class' => 'table human-collection'),
            ]);

        $choicesControl = $options['minute']->getControlsForFolder();

        $builder
            ->add('control', EntityType::class, array(
                'class' => 'LuccaMinuteBundle:Control', 'label' => 'label.control', 'required' => true,
                'multiple' => false, 'expanded' => false, 'choices' => $choicesControl,
                'attr' => array(
                    'class' => 'chosen-select js-control',
                    'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaMinuteBundle'),
                ),
                'choice_attr' => function (Control $control) {
                    return array('data-stateControl' =>$control->getStateControl());
                },
                'choice_label' => function (Control $control) {
                    return $control->getFormLabel() . ' (' . $this->translator->trans($control->getStateControl(), array(), 'LuccaControlBundle') . ')';
                },
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('minute');
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
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
