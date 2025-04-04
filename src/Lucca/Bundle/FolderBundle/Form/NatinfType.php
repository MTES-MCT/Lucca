<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType, IntegerType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\FolderBundle\Entity\{Natinf, Tag};

class NatinfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('num', IntegerType::class, array('label' => 'label.num', 'required' => true))
            ->add('enabled', CheckboxType::class, array('label' => 'label.enabled'))
            ->add('qualification', TextType::class, array('label' => 'label.qualification', 'required' => true))
            ->add('definedBy', TextType::class, array('label' => 'label.definedBy', 'required' => true))
            ->add('repressedBy', TextType::class, array('label' => 'label.repressedBy', 'required' => true))
            ->add('tags', EntityType::class, array(
                'class' => Tag::class, 'label' => 'label.tag', 'choice_label' => 'name', 'autocomplete' => true,
                'multiple' => true, 'required' => true, 'attr' => array('class' => 'tom-select')
            ))
            ->add('parent', EntityType::class, array(
                'class' => Natinf::class, 'label' => 'label.parent', 'choice_label' => 'num',
                'required' => false, 'attr' => array('class' => 'tom-select'), 'autocomplete' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Natinf::class,
            'translation_domain' => 'FolderBundle',
            'required' => false
        ));
    }
}
