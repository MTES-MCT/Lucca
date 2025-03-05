<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Form;

use Lucca\Bundle\ContentBundle\Entity\Area;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType, IntegerType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ContentBundle\Entity\SubArea;
use Lucca\Bundle\ContentBundle\Repository\AreaRepository;

class SubAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled'])
            ->add('position', IntegerType::class, ['label' => 'label.position', 'required' => true])
            ->add('width', TextType::class, ['label' => 'label.width', 'required' => true])
            ->add('color', TextType::class, ['label' => 'label.color', 'required' => true,
                'attr' => ['class' => 'form-control colorpicker-element']])
            ->add('title', TextType::class, ['label' => 'label.title', 'required' => true])
            ->add('area', EntityType::class, [
                'class' => Area::class, 'label' => 'label.area', 'choice_label' => 'name',
                'multiple' => false, 'expanded' => false, 'required' => true, 'attr' => ['class' => 'chosen-select'],
                'query_builder' => function (AreaRepository $repo) {
                    return $repo->getValuesActive();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubArea::class,
            'translation_domain' => 'LuccaContentBundle',
            'required' => false
        ]);
    }
}
