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
use Symfony\Component\Form\Extension\Core\Type\{EnumType, TextType, CheckboxType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\UserBundle\Config\Role;
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
            ->add('roles', EnumType::class, [
                'class' => Role::class, 'label' => 'label.roles', 'multiple' => true, 'expanded' => true
            ])
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
     * Get FOSUserBundle form parent
     * @return string
     */
    public function getParent(): string
    {
        return 'FOS\UserBundle\Form\Type\GroupFormType';
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_user_group';
    }
}
