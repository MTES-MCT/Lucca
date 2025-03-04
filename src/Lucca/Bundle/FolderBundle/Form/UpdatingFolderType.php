<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Human;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UpdatingFolderType
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class UpdatingFolderType extends AbstractType
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
                    Folder::NATURE_OBSTACLE => Folder::NATURE_OBSTACLE,
                    Folder::NATURE_OTHER => Folder::NATURE_OTHER,
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
/*            ->add('checklist', EntityType::class, array(
                'class' => 'LuccaChecklistBundle:Checklist', 'choice_label' => 'name',
                'multiple' => false, 'expanded' => false, 'label' => 'label.checklist', 'required' => false,
                'attr' => array('class' => 'chosen-select')
            ))*/
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
                'class' => 'LuccaFolderBundle:Human', 'label' => false, 'required' => false,
                'multiple' => true, 'expanded' => false, 'choices' => $choicesHuman,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaFolderBundle')
                ),
                'choice_label' => function (Human $human) {
                    return '(' . $this->translator->trans($human->getStatus(), array(), 'LuccaFolderBundle') . ') ' . $human->getOfficialName();
                },
            ));

        $builder
            ->add('humansByFolder', CollectionType::class, [
                'label' => false, 'entry_type' => HumanType::class,
                'allow_add' => true, 'allow_delete' => true, 'delete_empty' => true,
                'prototype' => true, 'required' => false, 'by_reference' => true,
                'entry_options' => array('attr' => array('class' => 'human')),
                'attr' => array('class' => 'table human-collection'),
            ]);

        $choicesControl = $options['updating']->getControlsForFolder();

        $builder
            ->add('control', EntityType::class, array(
                'class' => 'LuccaFolderBundle:Control', 'label' => 'label.control', 'required' => true,
                'multiple' => false, 'expanded' => false, 'choices' => $choicesControl,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaFolderBundle')
                ),
                'choice_label' => 'formLabel'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('minute');
        $resolver->setRequired('updating');
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
            'translation_domain' => 'LuccaFolderBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_folder';
    }
}
