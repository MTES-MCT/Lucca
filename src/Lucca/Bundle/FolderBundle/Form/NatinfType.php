<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class NatinfType
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class NatinfType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num', IntegerType::class, array('label' => 'label.num', 'required' => true))
            ->add('enabled', CheckboxType::class, array('label' => 'label.enabled'))
            ->add('qualification', TextType::class, array('label' => 'label.qualification', 'required' => true))
            ->add('definedBy', TextType::class, array('label' => 'label.definedBy', 'required' => true))
            ->add('repressedBy', TextType::class, array('label' => 'label.repressedBy', 'required' => true))
            ->add('tags', EntityType::class, array(
                'class' => 'LuccaFolderBundle:Tag', 'label' => 'label.tag', 'choice_label' => 'name',
                'multiple' => true, 'expanded' => false, 'required' => true, 'attr' => array('class' => 'select2')
            ))
            ->add('parent', EntityType::class, array(
                'class' => 'LuccaFolderBundle:Natinf', 'label' => 'label.parent', 'choice_label' => 'num',
                'multiple' => false, 'expanded' => false, 'required' => false, 'attr' => array('class' => 'chosen-select')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lucca\FolderBundle\Entity\Natinf',
            'translation_domain' => 'LuccaFolderBundle',
            'required' => false
        ));
    }
}
