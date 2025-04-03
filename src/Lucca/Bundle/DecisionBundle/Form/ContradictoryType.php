<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\Form;

use Lucca\Bundle\DecisionBundle\Entity\Contradictory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lucca\Bundle\CoreBundle\Form\Type\HtmlDateType;

class ContradictoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateNoticeDdtm', HtmlDateType::class, [
                'label' => 'label.dateNoticeDdtm', 'required' => false
            ])
            ->add('dateExecution', HtmlDateType::class, [
                'label' => 'label.dateExecution', 'required' => true
            ])
            ->add('dateAnswer', HtmlDateType::class, [
                'label' => 'label.dateAnswer', 'required' => true
            ])
            ->add('answer', TextareaType::class, ['label' => 'label.answer',
                'attr' => ['class' => 'summernote'], 'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contradictory::class,
            'translation_domain' => 'DecisionBundle',
            'required' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'lucca_decisionBundle_contradictory';
    }
}
