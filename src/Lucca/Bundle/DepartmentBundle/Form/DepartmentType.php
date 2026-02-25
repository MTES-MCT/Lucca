<?php

/*
 * copyright (c) 2025. numeric wave
 */

namespace Lucca\Bundle\DepartmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, FileType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $builder->getData()->getId() === null;

        $builder
            ->add('code', TextType::class, [
                'label' => 'label.code',
                'required' => true,
                'attr' => [
                    'placeholder' => 'ex: 35, 2A, 974',
                    'data-target' => 'dep-code'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => true,
                'row_attr' => ['class' => 'manual-field']
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'label.comment',
                'required' => false,
                'attr' => ['class' => 'summernote6'],
                'row_attr' => ['class' => 'manual-field']
            ]);

        if ($isNew) {
            $builder->add('autoImport', CheckboxType::class, [
                'label' => 'label.autoImportApiGouv',
                'mapped' => false,
                'required' => false,
                'data' => true,
                'attr' => ['class' => 'toggle-import-mode']
            ]);
        } else {
            $builder->add('showInHomePage', CheckboxType::class, ['label' => 'label.showInHomePage', 'required' => false]);
        }

        // --- Logic for conditional validation and requirements ---

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($isNew) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$data) {
                return;
            }

            // Check if auto-import is enabled in the submitted data
            $isAuto = $isNew && isset($data['autoImport']) && $data['autoImport'];

            // 1. Handle Code field validation
            $codeConstraints = [new NotBlank()];
            if ($isAuto) {
                // Stricter validation only for API import
                $codeConstraints[] = new Regex(pattern: '/^([0-9]{2,3}|2A|2B)$/', message: 'validator.department.invalidCode');
            }

            $form->add('code', TextType::class, [
                'label' => 'label.code',
                'constraints' => $codeConstraints,
                'attr' => ['placeholder' => 'ex: 35, 2A, 974']
            ]);

            // 2. Handle Towns file requirement
            $form->add('towns', FileType::class, [
                'label' => 'label.towns',
                'required' => !$isAuto, // Required only if manual
                'mapped' => false,
                'attr' => ['accept' => '.csv', 'class' => 'custom-file'],
                'row_attr' => ['class' => 'manual-field csv-upload-field']
            ]);
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            // Initial state for towns field (before submit)
            if (!$form->has('towns')) {
                $form->add('towns', FileType::class, [
                    'label' => 'label.towns',
                    'required' => true,
                    'mapped' => false,
                    'attr' => ['accept' => '.csv', 'class' => 'custom-file'],
                    'row_attr' => ['class' => 'manual-field csv-upload-field']
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
            'translation_domain' => 'DepartmentBundle',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lucca_departmentBundle_department';
    }
}
