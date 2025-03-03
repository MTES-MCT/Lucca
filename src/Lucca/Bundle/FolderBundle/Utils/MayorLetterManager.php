<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\MayorLetter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FolderManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class MayorLetterManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AdherentFinder
     */
    private $adherentFinder;


    /**
     * FolderManager constructor
     *
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     * @param AdherentFinder $adherentFinder
     */
    public function __construct(EntityManager $entityManager, TranslatorInterface $translator, AdherentFinder $adherentFinder)
    {
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->adherentFinder = $adherentFinder;
    }

    /**
     * Check all field it's not null
     *
     * @param $data
     * @param MayorLetter|null $mayorLetter
     *
     * @return string
     */
    public function checkMayorLetterField($data, MayorLetter $mayorLetter = null)
    {
        $message = "";

        /* Check all field is not null */
        if ( $mayorLetter == null && (
            !$data->gender || !$data->nameMayor || !$data->addressMayor || !$data->town || count($data->folders) == 0 )
        ) {
            $message = $this->translator->trans("text.mayorLetter.badField", array(), 'LuccaMinuteBundle');
        }

        return $message;
    }

    /**
     * Delete a Mayor Letter
     *
     * @param MayorLetter $mayorLetter
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteMayorLetter(MayorLetter $mayorLetter)
    {
        $this->em->remove($mayorLetter);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.mayor.letter';
    }

}