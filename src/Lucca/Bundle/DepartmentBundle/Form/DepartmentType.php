<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

/**
 * Class DepartmentType
 *
 * @package Lucca\Bundle\DepartmentBundle\Form
 */
class DepartmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('code', TextType::class, array('label' => 'label.code', 'required' => true, 'attr' => array('required' => true)))
            ->add('name', TextType::class, array('label' => 'label.name', 'required' => true))
            ->add('comment', TextareaType::class, array('label' => 'label.comment', 'required' => false, 'attr' => array('class' => 'summernote6')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Department::class,
            'translation_domain' => 'DepartmentBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_departmentBundle_department';
    }
}
