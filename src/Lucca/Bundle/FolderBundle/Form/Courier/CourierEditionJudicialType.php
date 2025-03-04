<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form\Courier;

use Lucca\Bundle\FolderBundle\Entity\CourierEdition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourierEditionJudicialType
 *
 * @package Lucca\Bundle\FolderBundle\Form\Courier
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierEditionJudicialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('judicialEdited', CheckboxType::class, array('label' => 'label.judicialEdited', 'required' => false))
            ->add('letterJudicial', TextareaType::class, array('label' => 'label.letterJudicial', 'required' => true,
                'attr' => array('class' => 'summernote-letter')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CourierEdition::class,
            'translation_domain' => 'LuccaFolderBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lucca_folderBundle_courierEditionJudicial';
    }
}
