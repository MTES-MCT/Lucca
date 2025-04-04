<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, ChoiceType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\AdherentBundle\Repository\AdherentRepository;
use Lucca\Bundle\ModelBundle\Entity\Model;
use Lucca\Bundle\ModelBundle\Entity\Page;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Service, Town};
use Lucca\Bundle\ParameterBundle\Repository\{IntercommunalRepository, ServiceRepository, TownRepository};

class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name', 'required' => true])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled'])
            ->add('size', ChoiceType::class, [
                'choices' => Model::getSizeChoice(),
                'label' => 'label.size',
            ])
            ->add('orientation', ChoiceType::class, [
                'choices' => Model::getOrientationChoice(),
                'label' => 'label.orientation',
            ])
            ->add('layout', ChoiceType::class, [
                'choices' => [
                    Model::LAYOUT_COVER => Model::LAYOUT_COVER,
                    Model::LAYOUT_SIMPLE => Model::LAYOUT_SIMPLE,
                ],
                'label' => 'label.layout',
            ])
            ->add('font', ChoiceType::class, [
                'choices' => [
                    Page::FONT_MARIANNE_MEDIUM => Page::FONT_MARIANNE_MEDIUM,
                    Page::FONT_MARIANNE_LIGHT => Page::FONT_MARIANNE_LIGHT,
                    Page::FONT_MARIANNE_REGULAR => Page::FONT_MARIANNE_REGULAR,
                    Page::FONT_MARIANNE_THIN => Page::FONT_MARIANNE_THIN,
                    Page::FONT_MARIANNE_EXTRABOLD => Page::FONT_MARIANNE_EXTRABOLD,
                    Page::FONT_MARIANNE_BOLD => Page::FONT_MARIANNE_BOLD,
                ],
                'label' => 'label.font',
                'attr' => ['class' => 'js-page-font']
            ])
            ->add('documents', ChoiceType::class, [
                'choices' => [
                    Model::DOCUMENTS_CONVOCATION_LETTER => Model::DOCUMENTS_CONVOCATION_LETTER,
                    Model::DOCUMENTS_ACCESS_LETTER => Model::DOCUMENTS_ACCESS_LETTER,
                    Model::DOCUMENTS_FOLDER => Model::DOCUMENTS_FOLDER,
                    Model::DOCUMENTS_JUDICIAL_LETTER => Model::DOCUMENTS_JUDICIAL_LETTER,
                    Model::DOCUMENTS_DDTM_LETTER => Model::DOCUMENTS_DDTM_LETTER,
                    Model::DOCUMENTS_OFFENDER_LETTER => Model::DOCUMENTS_OFFENDER_LETTER,
                ],
                'label' => 'label.documents', 'multiple' => true,
                'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
            ])
            ->add('color', TextType::class, ['label' => 'label.color', 'required' => false,
                'attr' => ['class' => 'form-control colorpicker-element']])
            ->add('background', TextType::class, ['label' => 'label.background', 'required' => false,
                'attr' => ['class' => 'form-control colorpicker-element']]);

        /** Depends on user privilege we need to display different data */
        if ($options['simpleActionNeeded']) {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        Model::TYPE_PRIVATE => Model::TYPE_PRIVATE,
                    ],
                    'label' => 'label.type',
                ]);
        } else {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        Model::TYPE_ORIGIN => Model::TYPE_ORIGIN,
                        Model::TYPE_PRIVATE => Model::TYPE_PRIVATE,
                        Model::TYPE_SHARED => Model::TYPE_SHARED,
                    ],
                    'label' => 'label.type', 'attr' => ['class' => 'js-type']
                ])
                ->add('owner', EntityType::class, [
                    'class' => Adherent::class, 'choice_label' => 'officialName', 'required' => false,
                    'multiple' => false, 'label' => 'label.owner',
                    'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
                    'query_builder' => function (AdherentRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ])
                ->add('sharedService', EntityType::class, [
                    'class' => Service::class, 'choice_label' => 'name', 'required' => false,
                    'label' => 'label.sharedService', 'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
                    'query_builder' => function (ServiceRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ])
                ->add('sharedIntercommunal', EntityType::class, [
                    'class' => Intercommunal::class, 'choice_label' => 'name', 'required' => false,
                    'label' => 'label.sharedIntercommunal', 'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
                    'query_builder' => function (IntercommunalRepository $repo) {
                        return $repo->getValuesActive();
                    }
                ])
                ->add('sharedTown', EntityType::class, [
                    'class' => Town::class, 'choice_label' => 'name', 'required' => false,
                    'label' => 'label.sharedTown', 'attr' => ['class' => 'tom-select'], 'autocomplete' => true,
                    'query_builder' => function (TownRepository $repo) {
                        return $repo->getValuesOrderedByName();
                    }
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
            'translation_domain' => 'ModelBundle',
            'required' => false,
            'simpleActionNeeded' => true
        ]);
    }
}
