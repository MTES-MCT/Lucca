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
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, IntegerType, TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ModelBundle\Entity\Page;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marginUnit', ChoiceType::class, [
                'choices' => Page::getMargUnits(),
                'label' => 'label.marginUnit',
            ])
//            TODO These data move in model for a first version of the module
//            ->add('font', ChoiceType::class, array(
//                'choices' => array(
//                    Page::FONT_MARIANNE_MEDIUM => Page::FONT_MARIANNE_MEDIUM,
//                    Page::FONT_MARIANNE_LIGHT => Page::FONT_MARIANNE_LIGHT,
//                    Page::FONT_MARIANNE_REGULAR => Page::FONT_MARIANNE_REGULAR,
//                    Page::FONT_MARIANNE_THIN => Page::FONT_MARIANNE_THIN,
//                    Page::FONT_MARIANNE_EXTRABOLD => Page::FONT_MARIANNE_EXTRABOLD,
//                    Page::FONT_MARIANNE_BOLD => Page::FONT_MARIANNE_BOLD,
//                ),
//                'label' => 'label.font',
//                'attr' => array('class' => 'js-page-font')
//            ))
//            ->add('color', TextType::class, array('label' => 'label.color', 'required' => false,
//                'attr' => array('class' => 'form-control colorpicker-element')))
//            ->add('background', TextType::class, array('label' => 'label.background', 'required' => false,
//                'attr' => array('class' => 'form-control colorpicker-element')))
            ->add('cssInline', TextareaType::class, [
                'label' => 'label.cssInline', 'required' => false
            ])

            ->add('headerSize', IntegerType::class, ['label' => 'label.headerSize', 'required' => false])
            ->add('footerSize', IntegerType::class, ['label' => 'label.footerSize', 'required' => false])
            ->add('leftSize', IntegerType::class, ['label' => 'label.leftSize', 'required' => false])
            ->add('rightSize', IntegerType::class, ['label' => 'label.rightSize', 'required' => false])
        ;

        /** Unmapped fields add to this form */
        $builder
            ->add('marginTop', MarginType::class, [
                'location' => 'header','label' => 'label.marginTop', 'required' => false, 'mapped' => true
            ])
            ->add('marginBottom', MarginType::class, [
                'location' => 'header', 'label' => 'label.marginBottom', 'required' => false, 'mapped' => true
            ])
            ->add('marginLeft', MarginType::class, [
                'location' => 'side', 'label' => 'label.marginLeft', 'required' => false, 'mapped' => true
            ])
            ->add('marginRight', MarginType::class, [
                'location' => 'side', 'label' => 'label.marginRight', 'required' => false, 'mapped' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'translation_domain' => 'LuccaModelBundle',
            'required' => false
        ]);
    }
}
