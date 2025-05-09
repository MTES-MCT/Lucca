<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\FolderBundle\Entity\{Folder, Natinf};

class FolderStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $natinfsFiltered = $options['natinfsFiltered'];

        $builder
            ->add('natinfs', EntityType::class, array(
                'class' => Natinf::class, 'choice_label' => 'num', 'choices' => $natinfsFiltered,
                'multiple' => true, 'expanded' => true, 'label' => 'label.natinfs', 'required' => false,
                'attr' => array('class' => 'tom-select'), 'autocomplete' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('natinfsFiltered');
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
        return 'lucca_folderBundle_folder_step3';
    }
}
