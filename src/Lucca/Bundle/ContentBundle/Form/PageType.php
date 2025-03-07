<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ContentBundle\Form;

use Lucca\Bundle\ContentBundle\Entity\Page;
use Lucca\Bundle\ContentBundle\Entity\SubArea;
use Lucca\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, TextareaType, CheckboxType, IntegerType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\ContentBundle\Repository\SubAreaRepository;
use Lucca\Bundle\UserBundle\Repository\UserRepository;
use Lucca\Bundle\MediaBundle\Form\Dropzone\DropzoneType;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled'])
            ->add('position', IntegerType::class, ['label' => 'label.position', 'required' => true])
            ->add('icon', TextType::class, ['label' => 'label.icon', 'required' => true])
            ->add('link', TextType::class, ['label' => 'label.link', 'required' => true])
            ->add('content', TextareaType::class, ['label' => 'label.content', 'required' => false,
                'attr' => ['class' => 'summernote']])
            ->add('author', EntityType::class, [
                'class' => User::class, 'label' => 'label.author', 'choice_label' => 'username',
                'multiple' => false, 'expanded' => false, 'required' => true, 'attr' => ['class' => 'chosen-select'],
                'query_builder' => function (UserRepository $repo) {
                    return $repo->getValuesActive();
                }
            ])
            ->add('subarea', EntityType::class, [
                'class' => SubArea::class, 'label' => 'label.subarea', 'choice_label' => 'name',
                'multiple' => false, 'expanded' => false, 'required' => true, 'attr' => ['class' => 'chosen-select'],
                'query_builder' => function (SubAreaRepository $repo) {
                    return $repo->getValuesActive();
                }
            ])
            ->add('mediasLinked', DropzoneType::class, [
                'label' => 'label.mediasLinked', 'required' => false, 'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'translation_domain' => 'ContentBundle',
            'required' => true
        ]);
    }
}
