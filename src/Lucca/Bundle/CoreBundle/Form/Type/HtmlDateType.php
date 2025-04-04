<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucca\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the custom form field type used to manipulate date values
 *
 * See https://symfony.com/doc/current/form/create_custom_field_type.html
 */
final class HtmlDateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        // @see https://symfony.com/doc/current/reference/forms/types/date.html#rendering-a-single-html5-text-box
        $resolver->setDefaults([
            'widget' => 'single_text',
            // if true, the browser will display the native date picker widget
            'html5' => true,
            'attr' => [
                'type' => 'date',
            ],
            'input_format' => 'Y-m-d',
        ]);
    }

    public function getParent(): ?string
    {
        return DateType::class;
    }
}
