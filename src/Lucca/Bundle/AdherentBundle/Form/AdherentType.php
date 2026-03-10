<?php

namespace Lucca\Bundle\AdherentBundle\Form;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\DepartmentBundle\Repository\DepartmentRepository;
use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Service, Town};
use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\ParameterBundle\Repository\{TownRepository, IntercommunalRepository, ServiceRepository};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface, FormEvent, FormEvents, FormInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, ChoiceType, TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdherentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $showDepartment = $options['showDepartment'];
        $user = $options['user'];
        $isNew = $options['data']->getId() === null;

        $builder
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('enabled', CheckboxType::class, ['label' => 'label.enabled', 'required' => false])
            ->add('firstname', TextType::class, ['label' => 'label.firstname', 'empty_data' => ''])
            ->add('function', ChoiceType::class, [
                'choices' => [
                    Adherent::FUNCTION_MAYOR => Adherent::FUNCTION_MAYOR,
                    Adherent::FUNCTION_DEPUTY => Adherent::FUNCTION_DEPUTY,
                    Adherent::FUNCTION_POLICE => Adherent::FUNCTION_POLICE,
                    Adherent::FUNCTION_DGS => Adherent::FUNCTION_DGS,
                    Adherent::FUNCTION_DST => Adherent::FUNCTION_DST,
                    Adherent::FUNCTION_ASVP => Adherent::FUNCTION_ASVP,
                    Adherent::FUNCTION_TOWN_MANAGER => Adherent::FUNCTION_TOWN_MANAGER,
                    Adherent::FUNCTION_ADMIN_AGENT => Adherent::FUNCTION_ADMIN_AGENT,
                    Adherent::FUNCTION_COUNTRY_GUARD => Adherent::FUNCTION_COUNTRY_GUARD,
                    Adherent::FUNCTION_COUNTRY_AGENT => Adherent::FUNCTION_COUNTRY_AGENT,
                ],
                'label' => 'label.function', 'expanded' => false, 'multiple' => false
            ])
            ->add('address', TextType::class, ['label' => 'label.address'])
            ->add('zipcode', TextType::class, ['label' => 'label.zipcode'])
            ->add('city', TextType::class, ['label' => 'label.city'])
            ->add('phone', TextType::class, ['label' => 'label.phone'])
            ->add('mobile', TextType::class, ['label' => 'label.mobile', 'required' => false]);

        // 1. Logic for Department field
        if ($showDepartment && $isNew) {
            $builder->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'formLabel',
                'attr' => ['class' => 'tom-select', 'data-action' => 'change->adherent-form#updateFields'], // For Stimulus or JS
                'label' => 'label.department',
                'required' => true,
                'placeholder' => 'label.selectDepartment',
                'autocomplete' => true,
                'query_builder' => function (DepartmentRepository $er) use ($user) {
                    if ($user instanceof User)
                        return $er->getNoExistingForAdherents($user);
                    else
                        return $er->getValuesActive();
                }
            ]);
        }

        if ($isNew && !$user) {
            $builder->add('user', UserType::class, ['required' => true]);
        }

        /**
         * 2. Helper function to add dependent fields
         * It takes the FormInterface and the selected Department (nullable)
         */
        $addDependentFields = function (FormInterface $form, ?Department $department = null) {
            // Town field
            $form->add('town', EntityType::class, [
                'class' => Town::class,
                'choice_label' => 'formLabel',
                'attr' => ['class' => 'tom-select'],
                'label' => 'label.town',
                'required' => false,
                'autocomplete' => true,
                'query_builder' => function (TownRepository $er) use ($department) {
                    return $er->getValuesActiveByDepartment($department);
                }
            ]);

            // Service field
            $form->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'formLabel',
                'attr' => ['class' => 'tom-select'],
                'label' => 'label.service',
                'required' => false,
                'autocomplete' => true,
                'query_builder' => function (ServiceRepository $er) use ($department) {
                    return $er->getValuesActiveByDepartment($department);
                }
            ]);

            // Intercommunal field
            $form->add('intercommunal', EntityType::class, [
                'class' => Intercommunal::class,
                'choice_label' => 'formLabel',
                'attr' => ['class' => 'tom-select'],
                'label' => 'label.intercommunal',
                'required' => false,
                'autocomplete' => true,
                'query_builder' => function (IntercommunalRepository $er) use ($department) {
                    return $er->getValuesActiveByDepartment($department);
                }
            ]);
        };

        // 3. Listener for initial data (Load)
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($addDependentFields) {
            $data = $event->getData();
            $form = $event->getForm();
            $department = ($data instanceof Adherent) ? $data->getDepartment() : null;
            $addDependentFields($form, $department);
        });

        // 4. Listener for dynamic changes (AJAX/Submit)
        // We listen to the department field specifically if it exists
        if ($showDepartment) {
            $builder->get('department')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($addDependentFields) {
                $form = $event->getForm();

                // Retrieve the department selected in the submitted form data
                $department = $form->getData();

                // Access the parent form (AdherentType) to get the underlying entity
                $parentForm = $form->getParent();
                $adherent = $parentForm->getData();

                /** * Important: When the department changes, we must reset dependent geographic fields
                 * to prevent data inconsistency (e.g., keeping a Town from the previous department)
                 */
                if ($adherent instanceof Adherent) {
                    $adherent->setTown(null);
                    $adherent->setService(null);
                    $adherent->setIntercommunal(null);
                }

                // Re-add dependent fields (Town, Service, Intercommunal) filtered by the newly selected Department
                $addDependentFields($parentForm, $department);
            });
        }

        $builder
            ->add('unitAttachment', TextType::class, ['label' => 'label.unitAttachment', 'required' => false])
            ->add('emailPublic', TextType::class, ['label' => 'label.emailPublic', 'required' => false])
            ->add('logo', MediaQuickType::class, [
                'label' => 'label.logo', 'required' => false, 'accept' => 'image/*'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'showDepartment' => false,
            'data_class' => Adherent::class,
            'user' => null,
            'translation_domain' => 'AdherentBundle',
            'required' => true
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lucca_adherent_type';
    }
}
