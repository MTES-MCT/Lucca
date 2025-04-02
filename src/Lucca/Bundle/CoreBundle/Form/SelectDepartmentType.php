<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Form;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\ExtensionCore\Type\{TextType, MoneyType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sparky\Bundle\AdherentBundle\Entity\Department;
use Sparky\Bundle\AdherentBundle\Repository\DepartmentRepository;

class SelectDepartmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('department', EntityType::class, [
                'class' => Department::class, 'choice_label' => 'formLabel', 'required' => true, 'autocomplete' => true,
                'label' => 'text.selectDepartment', 'attr' => ['class' => 'tom-select'],
                'query_builder' => function (DepartmentRepository $repo) use ($options): QueryBuilder {
                    return $repo->getByIds($options['ids']);
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'translation_domain' => 'CoreBundle',
            'required' => true,
            'mapped' => false,
            'ids' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_coreBundle_selectDepartment';
    }
}
