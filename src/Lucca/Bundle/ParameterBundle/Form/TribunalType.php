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
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType, CountryType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ParameterBundle\Entity\{Town, Tribunal};

class TribunalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, ['label' => 'label.code'])
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('interlocutor', TextType::class, ['label' => 'label.interlocutor', 'required' => false])
            ->add('address', TextType::class, ['label' => 'label.address', 'required' => false])
            ->add('addressCpl', TextType::class, ['label' => 'label.addressCpl', 'required' => false])
            ->add('zipCode', TextType::class, ['label' => 'label.zipcode', 'required' => false])
            ->add('city', TextType::class, ['label' => 'label.city', 'required' => false])
            ->add('region', TextType::class, ['label' => 'label.region', 'required' => false])
            ->add('country', CountryType::class, ['label' => 'label.country', 'preferred_choices' => ['FR'],
                'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
            ])
            ->add('office', EntityType::class, [
                'class' => Town::class, 'label' => 'label.office', 'choice_label' => 'formLabel',
                'required' => false, 'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tribunal::class,
            'translation_domain' => 'ParameterBundle',
            'required' => true
        ]);
    }
}
