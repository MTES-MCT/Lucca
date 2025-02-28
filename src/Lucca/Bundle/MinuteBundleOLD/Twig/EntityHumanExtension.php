<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Twig;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Human;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EntityHumanExtension
 *
 * @package Lucca\MinuteBundle\Twig
 * @author Térence <terence@numeric-wave.eu>
 */
class EntityHumanExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FolderCreator constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get twig filters
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('human_inlineDescription', array($this, 'getInlineDescription'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('human_inlineName', array($this, 'getInlineName'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('human_genderName', array($this, 'getGenderName'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('human_gender', array($this, 'getGender'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('human_address', array($this, 'getAddress'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Format Json data
     *
     * @param $json
     * @return mixed
     */
    public function getInlineDescription(Human $human)
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION) {
            $inlineDescription .= $human->getCompany() . ' ';
            $inlineDescription .= '(' . $this->translator->trans($human->getStatusCompany(), array(), 'LuccaMinuteBundle') . ') <br>';
            $inlineDescription .= $human->getAddressCompany() . ' <br>';
        }

        $inlineDescription .= $this->translator->trans($human->getGender(), array(), 'LuccaMinuteBundle') . ' ';
        $inlineDescription .= $human->getOfficialName();
        $inlineDescription .= '(' . $this->translator->trans($human->getStatus(), array(), 'LuccaMinuteBundle') . ')  <br>';
        $inlineDescription .= $human->getAddress();

        return $inlineDescription;
    }

    /**
     * Inline name of Human
     *
     * @param Human $human
     * @return string
     */
    public function getInlineName(Human $human)
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION) {
            $inlineDescription .= $this->translator->trans('label.company', array(), 'LuccaMinuteBundle') . ' ';
            $inlineDescription .= $human->getCompany() . ' représenté par ';
        }

        $inlineDescription .= $this->translator->trans($human->getGender(), array(), 'LuccaMinuteBundle') . ' ';
        $inlineDescription .= $human->getOfficialName();

        return $inlineDescription;
    }

    /**
     * Return gender + name of human
     *
     * @param Human $human
     * @param bool $flagRepresentant
     * @return string
     */
    public function getGenderName(Human $human, $flagRepresentant = true)
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION && $flagRepresentant) {
            $inlineDescription .= $this->translator->trans('label.company', array(), 'LuccaMinuteBundle') . ' ';
            $inlineDescription .= $human->getCompany();
        } else {
            $inlineDescription .= $this->translator->trans($human->getGender(), array(), 'LuccaMinuteBundle') . ' ';
            $inlineDescription .= $human->getOfficialName();
        }

        return $inlineDescription;
    }

    /**
     * Return Gender of a Human Entity
     *
     * @param Human $human
     * @return string
     */
    public function getGender(Human $human)
    {
        $inlineDescription = '';

        if ($human->getGender() === Human::GENDER_MALE)
            $inlineDescription .= $this->translator->trans('choice.gender.male', array(), 'LuccaMinuteBundle');
        else
            $inlineDescription .= $this->translator->trans('choice.gender.female', array(), 'LuccaMinuteBundle');

        return $inlineDescription;
    }

    /**
     * Return gender + name of human
     *
     * @param Human $human
     * @param bool $flagRepresentant
     * @return string
     */
    public function getAddress(Human $human, $flagRepresentant = true)
    {
        $inlineDescription = '';

        if ($human->getPerson() === Human::PERSON_CORPORATION && $flagRepresentant) {
            $inlineDescription .= $human->getAddressCompany();
        } else {
            $inlineDescription .= $human->getAddress();
        }

        return $inlineDescription;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'lucca.twig.entity.human';
    }
}
