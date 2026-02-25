<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, FileType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $builder->getData()->getId() === null;

        $builder
            // Added Regex for department codes (01-99, 2A, 2B, 971-976)
            ->add('code', TextType::class, [
                'label' => 'label.code',
                'required' => true,
                'attr' => [
                    'placeholder' => 'ex: 35, 2A, 974',
                    'data-target' => 'dep-code'
                ],
                'constraints' => [
                    new Regex(pattern: '/^([0-9]{2,3}|2A|2B)$/', message: 'validator.department.invalidCode')
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
            // Checkbox to toggle between API Gouv and Manual import
            $builder->add('autoImport', CheckboxType::class, [
                'label' => 'label.autoImportApiGouv',
                'mapped' => false,
                'required' => false,
                'data' => true, // Default checked
                'attr' => ['class' => 'toggle-import-mode']
            ]);
        } else {
            $builder->add('showInHomePage', CheckboxType::class, ['label' => 'label.showInHomePage', 'required' => false]);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $form->add('towns', FileType::class, [
                'label' => 'label.towns',
                'required' => true,
                'mapped' => false,
                'attr' => ['accept' => '.csv', 'class' => 'custom-file'],
                'row_attr' => ['class' => 'manual-field csv-upload-field']
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Department::class,
            'translation_domain' => 'DepartmentBundle',
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'lucca_departmentBundle_department';
    }
}
