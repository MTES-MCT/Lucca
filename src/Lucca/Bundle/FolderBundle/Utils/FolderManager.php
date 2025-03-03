<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\ElementChecked;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Lucca\Bundle\MinuteBundle\Entity\Control;
use Lucca\Bundle\MinuteBundle\Entity\Minute;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Lucca\Bundle\ChecklistBundle\Entity\Checklist;
use Lucca\Bundle\MinuteBundle\Generator\NumFolderGenerator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FolderManager
 *
 * @package Lucca\Bundle\MinuteBundle\Utils
 * @author Térence <terence@numeric-wave.tech>
 */
class FolderManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FolderEditionManager
     */
    private $folderEditionManager;

    /**
     * @var NumFolderGenerator
     */
    private $numFolderGenerator;

    /**
     * @var CourierManager
     */
    private $courierManager;

    /**
     * @var $avoidBreakPageTags
     */
    private $avoidBreakPageTags;

    /**
     * FolderManager constructor
     *
     * @param EntityManager $entityManager
     * @param FolderEditionManager $folderEditionManager
     * @param NumFolderGenerator $numFolderGenerator
     * @param CourierManager $courierManager
     * @param $p_avoidBreakPageTags
     */
    public function __construct(EntityManager $entityManager, FolderEditionManager $folderEditionManager,
                                NumFolderGenerator $numFolderGenerator, CourierManager $courierManager,
                                $p_avoidBreakPageTags)
    {
        $this->em = $entityManager;
        $this->folderEditionManager = $folderEditionManager;

        $this->numFolderGenerator = $numFolderGenerator;
        $this->courierManager = $courierManager;

        $this->avoidBreakPageTags = explode(',', $p_avoidBreakPageTags);
    }

    /**
     * Create a specific folder with Obstacle Nature
     *
     * @param Minute $p_minute
     * @param Control $p_control
     * @param $p_type
     * @return bool|Folder
     */
    public function createObstacleFolder(Minute $p_minute, Control $p_control, $p_type)
    {
        $folders = $this->em->getRepository('LuccaMinuteBundle:Folder')->findBy(array(
            'minute' => $p_minute, 'control' => $p_control, 'nature' => Folder::NATURE_OBSTACLE
        ));

        if (count($folders) > 0)
            return false;

        $folder = new Folder();
        $folder->setMinute($p_minute);
        $folder->setControl($p_control);
        $folder->setNature(Folder::NATURE_OBSTACLE);

        if ($p_type === Folder::TYPE_FOLDER) {
            /** Classic Folder */
            $folder->setType(Folder::TYPE_FOLDER);

        } elseif ($p_type === Folder::TYPE_REFRESH) {
            /** Refresh Folder */
            $folder->setType(Folder::TYPE_REFRESH);

        } else
            throw new NotFoundHttpException('Bad Folder type given');

        /** Create folder num */
        $folder->setNum($this->numFolderGenerator->generate($folder));

        /** Add all default element to checklist */
        $folder = $this->addChecklistToFolder($folder);

        /** Create / update / delete editions if needed */
        $this->folderEditionManager->manageEditionsOnFormSubmission($folder);

        $this->configureObstacleFolder($folder);

        try {
            $this->em->persist($folder);
        } catch (ORMException $ORMException) {
            echo 'Error to create Obstacle folder - ' . $ORMException->getMessage();
        }

        return $folder;
    }

    /**
     * Configure parameters for Obstacle folder
     *
     * @param Folder $p_folder
     * @return bool|Folder
     */
    public function configureObstacleFolder(Folder $p_folder)
    {
        if ($p_folder->hasNatinf('33058'))
            return false;

        /** Specific Natinf */
        $natinfObstacle = $this->em->getRepository('LuccaMinuteBundle:Natinf')->findOneBy(array(
            'num' => '33058'
        ));

        $p_folder->addNatinf($natinfObstacle);

        return $p_folder;
    }

    /**
     * Ad all default element on folder
     * @param Folder $p_folder
     * @return Folder
     */
    public function addChecklistToFolder(Folder $p_folder)
    {
        if ($p_folder->getType() === Folder::TYPE_REFRESH)
            $status = Checklist::STATUS_UPDATING;
        else
            $status = Checklist::STATUS_MINUTE;

        /** Check if a standard checklist exist */
        $list = $this->em->getRepository('LuccaChecklistBundle:Checklist')->findOneBy(array(
            'status' => $status
        ));

        /** If list exist - add list + all elements to folder */
        if ($list) {
            $elements = $this->em->getRepository('LuccaChecklistBundle:Element')->findActiveByChecklist($list);

            foreach ($elements as $element)
                $p_folder->addElement(new ElementChecked($p_folder, $element));
        }

        return $p_folder;
    }

    /**
     * Close folder and create Courier
     *
     * @param Folder $p_folder
     * @return bool
     */
    public function closeFolder(Folder $p_folder)
    {
        if ($p_folder->getDateClosure())
            return false;

        /** If the folder is edited manually we need to replace the custom tag in the edited version */
        if($p_folder->getEdition()->getFolderEdited()){
            $p_folder->getEdition()->setFolderVersion($this->replaceCustomTags($p_folder->getEdition()->getFolderVersion()));
        }

        /** Replace tag added by the user to indicate part of text that can't have page break inside */
        $p_folder->setAscertainment($this->replaceCustomTags($p_folder->getAscertainment()));
        $p_folder->setDetails($this->replaceCustomTags($p_folder->getDetails()));
        $p_folder->setViolation($this->replaceCustomTags($p_folder->getViolation()));

        $p_folder->setDateClosure(new \Datetime('now'));
        $p_folder->getControl()->setIsFenced(true);

        $this->courierManager->createCourierToFolder($p_folder);

        try {
            $this->em->persist($p_folder->getControl());
            $this->em->persist($p_folder);
        } catch (ORMException $ORMException) {
            echo 'Error to close Obstacle folder - ' . $ORMException->getMessage();
        }

        return true;
    }

    /**
     * Purge empty element on Folder entity
     *
     * @param Folder $p_folder
     * @return Folder $p_folder
     */
    public function purgeChecklist(Folder $p_folder)
    {
        $elements = $p_folder->getElements();

        /** Loop for Elements founded */
        foreach ($elements as $element) {
            /**  If element is not checked - delete it */
            if (!$element->getState()) {
                try {
                    $this->em->remove($element);
                } catch (ORMException $ORMException) {
                    echo 'Error to purge element of Checklist - ' . $ORMException->getMessage();
                }
            }
        }

        return $p_folder;
    }

    /**
     * This function is used to replace tags that the user had to specify which part of the text can't have a page break inside
     *
     * @param $p_data
     * @return mixed|string|string[]
     */
    public function replaceCustomTags($p_data)
    {
        /** Search if there is custom tag in the html */
        $startOffset = strpos($p_data, $this->avoidBreakPageTags[0]);
        $endOffset = strpos($p_data, $this->avoidBreakPageTags[1]);

        /** If we find at least one of them we need to do something */
        while($startOffset || $endOffset){
            /** If we find a pair of starting and ending tags we can replace by the html elements */
            if ($startOffset && $endOffset){
                $p_data = substr_replace($p_data, '<div class="page-break-inside-avoid">', $startOffset, strlen($this->avoidBreakPageTags[0]) );
                $endOffset = strpos($p_data, $this->avoidBreakPageTags[1]);
                $p_data = substr_replace($p_data, '</div>', $endOffset, strlen($this->avoidBreakPageTags[1]) );
            }else{ /** But if we find only one tag and not a pair we need to remove it from the html */
                $p_data = str_replace($this->avoidBreakPageTags[0], "", $p_data);
                $p_data = str_replace($this->avoidBreakPageTags[1], "", $p_data);
            }

            /** Each loop we need to check again if there is still some custom tags */
            $startOffset = strpos($p_data, $this->avoidBreakPageTags[0]);
            $endOffset = strpos($p_data, $this->avoidBreakPageTags[1]);
        }
        return $p_data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.folder';
    }
}
