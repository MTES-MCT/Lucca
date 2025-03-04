<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{IntegerType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Lucca\Bundle\ModelBundle\Entity\Margin;

class MarginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // hide and show in function of if margin is on side or if is header/footer
        if ($options["location"] == "header") {
            $builder->add('height', IntegerType::class, ['label' => 'label.height', 'required' => false]);
        } else if ($options["location"] == "side") {
            $builder->add('width', IntegerType::class, ['label' => 'label.width', 'required' => false]);
        }

        $builder
            ->add('background', TextType::class, ['label' => 'label.background', 'required' => false,
                'attr' => ['class' => 'form-control colorpicker-element']])
            ->add('backgroundImg', MediaQuickType::class, [
                'label' => 'label.backgroundImg', 'isImage' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->
            setRequired('location')
        ;

        $resolver->setDefaults([
            'data_class' => Margin::class,
            'translation_domain' => 'LuccaModelBundle',
            'required' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_modelbundle_page_margin';
    }
}
