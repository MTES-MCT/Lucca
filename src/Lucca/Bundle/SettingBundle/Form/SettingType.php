<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface, FormEvent, FormEvents};
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    ChoiceType,
    NumberType,
    PercentType,
    IntegerType,
    TextareaType,
    TextType,
};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\SettingBundle\Entity\Setting;

class SettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var Setting $setting */
                $setting = $event->getData();
                $form = $event->getForm();

                switch ($setting->getType())
                {
                    /** Integer type -> no scale */
                    case Setting::TYPE_INTEGER :
                        $form->add('value', IntegerType::class, array(
                            'label' => 'label.value', 'required' => true, 'scale' => 0,
                            'attr' => array(
                                'class' => 'touchSpinInput',
                            ),
                        ));
                        $setting->setValue(intval($setting->getValue()));
                        break;
                    /** Float type -> 3 scale */
                    case Setting::TYPE_FLOAT :
                        $form->add('value', NumberType::class, array(
                            'label' => 'label.value',
                            'scale' => 3, 'required' => true,
                            'attr' => array(
                                'class' => 'touchSpinInputFloat',
                            ),
                        ));
                        $setting->setValue(floatval($setting->getValue()));
                        break;
                    /** Percent type -> 3 scale */
                    case Setting::TYPE_PERCENT:
                        $form->add('value', PercentType::class, array(
                            'label' => 'label.value',
                            'required' => true,
                        ));
                        $setting->setValue($setting->getValue());
                        break;
                    default:
                        /** Text type and default type */
                    case Setting::TYPE_TEXT:
                        $form->add('value', TextType::class, array(
                            'label' => 'label.value',
                            'required' => true,
                        ));
                        $setting->setValue($setting->getValue());
                        break;
                    case Setting::TYPE_BOOL :
                        $form->add('value', CheckboxType::class, array(
                            'label' => 'label.yesNo',
                            'required' => false,
                        ));
                        break;
                    case Setting::TYPE_LIST :
                        $form->add('value', ChoiceType::class, array(
                            'choices' => array_combine($setting->getvaluesAvailable(), $setting->getvaluesAvailable()),
                            'label' => 'label.value',
                        ));
                        break;
                    case Setting::TYPE_COLOR :
                        $form->add('value', TextType::class, array(
                            'label' => 'label.value',
                            'required' => false,
                            'attr' => array('class' => 'value-colorpicker'),
                        ));
                        break;
                    case Setting::TYPE_TEXT_LARGE:
                    $form->add('value', TextareaType::class, array(
                        'label' => 'label.value',
                        'required' => true,
                        'attr' => [
                            'rows' => 20,
                            'style' => 'background-color: #fcfcfc',
                            'placeholder' => 'text.empty',
                            'class' => 'text-monospace',
                        ],
                        'trim' => false,
                    ));
                    $setting->setValue($setting->getValue());
                    break;
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Setting::class,
            'translation_domain' => 'LuccaSettingBundle',
            'required' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_settingbundle_setting';
    }
}
