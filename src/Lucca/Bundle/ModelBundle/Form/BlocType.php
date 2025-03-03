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
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, ColorType, IntegerType, TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Lucca\Bundle\ModelBundle\Entity\Bloc;

class BlocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('height', IntegerType::class, ['label' => 'label.height', 'required' => true])
            ->add('width', IntegerType::class, ['label' => 'label.width', 'required' => true])
            ->add('typeContent', ChoiceType::class, [
                'choices' => [
                    Bloc::TYPE_CONTENT_HTML => Bloc::TYPE_CONTENT_HTML,
                    Bloc::TYPE_CONTENT_MEDIA => Bloc::TYPE_CONTENT_MEDIA,
                    Bloc::TYPE_CONTENT_ADHERENT_LOGO => Bloc::TYPE_CONTENT_ADHERENT_LOGO,
                ],
                'label' => 'label.typeContent', 'required' => true, 'attr' => ['class' => 'js-typeContent']
            ])
            ->add('headerSize', IntegerType::class, ['label' => 'label.headerSize', 'required' => false])
            ->add('footerSize', IntegerType::class, ['label' => 'label.footerSize', 'required' => false])
            ->add('leftSize', IntegerType::class, ['label' => 'label.leftSize', 'required' => false])
            ->add('rightSize', IntegerType::class, ['label' => 'label.rightSize', 'required' => false])
            ->add('content', TextareaType::class, ['label' => 'label.content',
                'required' => false, 'attr' => ["rows" => "7"],
            ])
            ->add('cssInline', TextareaType::class, ['label' => 'label.cssInline',
                'required' => false, 'attr' => ["rows" => "3"],
            ])
            ->add('color', ColorType::class, ['label' => 'label.color', 'required' => false,
                'attr' => ['class' => 'form-control']])
            ->add('backgroundImg', MediaQuickType::class, [
                'label' => 'label.backgroundImg', 'required' => false, 'isImage' => true
            ])
            ->add('media', MediaQuickType::class, [
                'label' => 'label.media', 'required' => false, 'isImage' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bloc::class,
            'translation_domain' => 'LuccaModelBundle',
            'required' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_modelbundle_bloc';
    }
}
