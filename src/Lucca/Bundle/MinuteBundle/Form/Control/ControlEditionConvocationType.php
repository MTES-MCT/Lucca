<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Form\Control;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\MinuteBundle\Entity\ControlEdition;

class ControlEditionConvocationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('convocationEdited', CheckboxType::class, array('label' => 'label.convocationEdited', 'required' => false))
            ->add('letterConvocation', TextareaType::class, array('label' => 'label.letterConvocation', 'required' => true,
                'attr' => array('class' => 'summernote-letter')
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => ControlEdition::class,
            'translation_domain' => 'MinuteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_minuteBundle_controlEdition_convocation';
    }
}
