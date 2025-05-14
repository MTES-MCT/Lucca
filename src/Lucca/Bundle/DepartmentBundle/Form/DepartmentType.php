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
use Symfony\Component\Form\Extension\Core\Type\{FileType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

class DepartmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('code', TextType::class, array('label' => 'label.code', 'required' => true, 'attr' => array('required' => true)))
            ->add('name', TextType::class, array('label' => 'label.name', 'required' => true))
            ->add('domainName', TextType::class, array('label' => 'label.domainName', 'required' => true, 'attr' => array('required' => true)))
            ->add('comment', TextareaType::class, array('label' => 'label.comment', 'required' => false, 'attr' => array('class' => 'summernote6')));

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data->getId() === null) {
                $form->add('towns', FileType::class, array(
                    'label' => 'label.towns', 'required' => true, 'mapped' => false, 'help' => 'help.towns',
                    'attr' => array('accept' => '.csv', 'class' => 'custom-file'),
                ));
            }
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

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_departmentBundle_department';
    }
}
