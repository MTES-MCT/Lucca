<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Form;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\MediaBundle\Form\Media\MediaQuickType;

class AdherentEditType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('label' => 'label.name'))
            ->add('enabled', CheckboxType::class, array('label' => 'label.enabled', 'required' => false))
            ->add('firstname', TextType::class, array('label' => 'label.firstname'))
            ->add('function', ChoiceType::class, array(
                'choices' => array(
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
                ),
                'label' => 'label.function', 'expanded' => false, 'multiple' => false
            ))
            ->add('address', TextType::class, array('label' => 'label.address'))
            ->add('zipcode', TextType::class, array('label' => 'label.zipcode'))
            ->add('city', TextType::class, array('label' => 'label.city'))
            ->add('phone', TextType::class, array('label' => 'label.phone'))
            ->add('mobile', TextType::class, array('label' => 'label.mobile', 'required' => false))
            ->add('unitAttachment', TextType::class, array('label' => 'label.unitAttachment', 'required' => false))
            ->add('emailPublic', TextType::class, array('label' => 'label.emailPublic', 'required' => false))
            ->add('logo', MediaQuickType::class, array(
                'label' => 'label.logo', 'required' => false,
            ));

        $builder
            ->add('user', UserType::class, array('required' => true));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Adherent::class,
            'translation_domain' => 'LuccaAdherentBundle',
            'required' => true
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_adherent_type';
    }
}
