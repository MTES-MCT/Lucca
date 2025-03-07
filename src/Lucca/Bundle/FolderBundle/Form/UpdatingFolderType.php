<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, CollectionType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human;
use Lucca\Bundle\MinuteBundle\Form\HumanType;

class UpdatingFolderType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'class' => Checklist::class, 'choice_label' => 'name',
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
                'class' => Human::class, 'label' => false, 'required' => false,
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
                'class' => Control::class, 'label' => 'label.control', 'required' => true,
                'multiple' => false, 'expanded' => false, 'choices' => $choicesControl,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => $this->translator->trans('help.data.select', array(), 'LuccaFolderBundle')
                ),
                'choice_label' => 'formLabel'
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('minute');
        $resolver->setRequired('updating');
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
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
