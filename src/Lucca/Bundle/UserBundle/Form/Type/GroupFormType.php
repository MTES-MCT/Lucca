<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextType, CheckboxType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\UserBundle\Entity\Group;

class GroupFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('displayed', CheckboxType::class, ['label' => 'label.displayed'])
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'choice.roles.superAdmin' => 'ROLE_SUPER_ADMIN',
                    'choice.roles.admin' => 'ROLE_ADMIN',
                    'choice.roles.lucca' => 'ROLE_LUCCA',
                    'choice.roles.visu' => 'ROLE_VISU',
                    'choice.roles.openFolder' => 'ROLE_FOLDER_OPEN',
                    'choice.roles.deleteFolder' => 'ROLE_DELETE_FOLDER',
                ),
                'multiple' => true, 'expanded' => true, 'required' => true, 'label' => 'label.roles'
            ));
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Group::class,
            'translation_domain' => 'LuccaUserBundle',
            'required' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_user_group';
    }
}
