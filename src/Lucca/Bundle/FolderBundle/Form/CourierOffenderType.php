<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Lucca\Bundle\FolderBundle\Entity\CourierEdition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourierOffenderType
 *
 * @package Lucca\Bundle\FolderBundle\Form
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierOffenderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activated', CheckboxType::class, array('label' => 'text.activatedManuallyEditing',
                'mapped' => false, 'required' => false
            ))
            ->add('letterOffender', TextareaType::class, array('label' => 'label.letterOffender', 'required' => true,
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
        return 'lucca_folderBundle_courier_offender';
    }
}
