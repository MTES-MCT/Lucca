<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ParameterBundle\Form\Autocomplete;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use Lucca\Bundle\ParameterBundle\Entity\Town;
use Lucca\Bundle\ParameterBundle\Repository\TownRepository;

#[AsEntityAutocompleteField]
class TownAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Town::class,

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['name'],

            // if the autocomplete endpoint needs to be secured
            //'security' => 'ROLE_FOOD_ADMIN',

            // ... any other normal EntityType options
            // e.g. query_builder, choice_label
            'choice_label' => 'name',
            'query_builder' => function (TownRepository $repo) {
                return $repo->getValuesActive();
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
