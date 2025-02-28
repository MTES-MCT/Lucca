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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FolderStep3Type
 *
 * @package Lucca\MinuteBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class FolderStep3Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $natinfsFiltered = $options['natinfsFiltered'];

        $builder
            ->add('natinfs', EntityType::class, array(
                'class' => 'LuccaMinuteBundle:Natinf', 'choice_label' => 'num', 'choices' => $natinfsFiltered,
                'multiple' => true, 'expanded' => true, 'label' => 'label.natinfs', 'required' => false,
                'attr' => array('class' => 'chosen-select',)
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('natinfsFiltered');
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
        return 'lucca_minutebundle_folder_step3';
    }
}
