<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ParameterBundle\Entity\Intercommunal;

class IntercommunalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, ['label' => 'label.code'])
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('office', EntityType::class, [
                'class' => 'LuccaParameterBundle:Town', 'label' => 'label.office', 'choice_label' => 'formLabel',
                'multiple' => false, 'expanded' => false, 'required' => false, 'attr' => ['class' => 'chosen-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intercommunal::class,
            'translation_domain' => 'LuccaParameterBundle',
            'required' => true
        ]);
    }
}
