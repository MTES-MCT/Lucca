<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ContentBundle\Entity\Area;

class AreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled'])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    Area::POSI_CONTENT => Area::POSI_CONTENT,
                    Area::POSI_FOOTER => Area::POSI_FOOTER,
                    Area::POSI_ADMIN => Area::POSI_ADMIN,
                ],
                'label' => 'label.position',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Area::class,
            'translation_domain' => 'ContentBundle',
            'required' => false
        ]);
    }
}
