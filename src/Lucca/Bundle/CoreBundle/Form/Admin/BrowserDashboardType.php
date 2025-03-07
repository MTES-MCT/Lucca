<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BrowserDashboardType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', DateTimeType::class, options: array(
                'label' => 'label.dateStart', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false,
                'input' => 'datetime', 'attr' => ['class' => 'date-picker']
            ))
            ->add('dateEnd', DateTimeType::class, [
                'label' => 'label.dateEnd', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false,
                'input' => 'datetime', 'attr' => ['class' => 'date-picker']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'mapped' => false,
            'translation_domain' => 'LuccaCoreBundle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_coreBundle_browser_dashboard';
    }
}
