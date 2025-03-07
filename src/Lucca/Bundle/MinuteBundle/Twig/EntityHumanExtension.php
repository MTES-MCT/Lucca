<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Lucca\Bundle\MinuteBundle\Entity\Human;

class EntityHumanExtension extends AbstractExtension
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('human_inlineDescription', [$this, 'getInlineDescription'], ['is_safe' => ['html']]),
            new TwigFilter('human_inlineName', [$this, 'getInlineName'], ['is_safe' => ['html']]),
            new TwigFilter('human_genderName', [$this, 'getGenderName'], ['is_safe' => ['html']]),
            new TwigFilter('human_gender', [$this, 'getGender'], ['is_safe' => ['html']]),
            new TwigFilter('human_address', [$this, 'getAddress'], ['is_safe' => ['html']]),
        );
    }

    /**
     * Format Json data
     */
    public function getInlineDescription(Human $human): string
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION) {
            $inlineDescription .= $human->getCompany() . ' ';
            $inlineDescription .= '(' . $this->translator->trans($human->getStatusCompany(), [], 'MinuteBundle') . ') <br>';
            $inlineDescription .= $human->getAddressCompany() . ' <br>';
        }

        $inlineDescription .= $this->translator->trans($human->getGender(), [], 'MinuteBundle') . ' ';
        $inlineDescription .= $human->getOfficialName();
        $inlineDescription .= '(' . $this->translator->trans($human->getStatus(), [], 'MinuteBundle') . ')  <br>';
        $inlineDescription .= $human->getAddress();

        return $inlineDescription;
    }

    /**
     * Inline name of Human
     */
    public function getInlineName(Human $human): string
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION) {
            $inlineDescription .= $this->translator->trans('label.company', [], 'MinuteBundle') . ' ';
            $inlineDescription .= $human->getCompany() . ' représenté par ';
        }

        $inlineDescription .= $this->translator->trans($human->getGender(), [], 'MinuteBundle') . ' ';
        $inlineDescription .= $human->getOfficialName();

        return $inlineDescription;
    }

    /**
     * Return gender + name of human
     */
    public function getGenderName(Human $human, bool $flagRepresentant = true): string
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION && $flagRepresentant) {
            $inlineDescription .= $this->translator->trans('label.company', [], 'MinuteBundle') . ' ';
            $inlineDescription .= $human->getCompany();
        } else {
            $inlineDescription .= $this->translator->trans($human->getGender(), [], 'MinuteBundle') . ' ';
            $inlineDescription .= $human->getOfficialName();
        }

        return $inlineDescription;
    }

    /**
     * Return Gender of a Human Entity
     */
    public function getGender(Human $human): string
    {
        $inlineDescription = '';

        if ($human->getGender() === Human::GENDER_MALE) {
            $inlineDescription .= $this->translator->trans('choice.gender.male', array(), 'LuccaMinuteBundle');
        } else {
            $inlineDescription .= $this->translator->trans('choice.gender.female', array(), 'LuccaMinuteBundle');
        }

        return $inlineDescription;
    }

    /**
     * Return gender + name of human
     */
    public function getAddress(Human $human, bool $flagRepresentant = true): string
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION && $flagRepresentant) {
            $inlineDescription .= $human->getAddressCompany();
        } else {
            $inlineDescription .= $human->getAddress();
        }

        return $inlineDescription;
    }

    public function getName(): string
    {
        return 'lucca.twig.entity.human';
    }
}
