<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Utils;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Lucca\Bundle\ChecklistBundle\Entity\{Element, Checklist};
use Lucca\Bundle\FolderBundle\Entity\{Folder, ElementChecked, Natinf};
use Lucca\Bundle\FolderBundle\Generator\NumFolderGenerator;
use Lucca\Bundle\MinuteBundle\Entity\{Control, Minute};

readonly class FolderManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private FolderEditionManager   $folderEditionManager,
        private NumFolderGenerator     $numFolderGenerator,
        private CourierManager         $courierManager,
        private ParameterBagInterface  $params,
    )
    {
    }

    /**
     * Create a specific folder with Obstacle Nature
     */
    public function createObstacleFolder(Minute $minute, Control $control, $type): Folder|bool
    {
        $folders = $this->em->getRepository(Folder::class)->findBy([
            'minute' => $minute, 'control' => $control, 'nature' => Folder::NATURE_OBSTACLE
        ]);

        if (count($folders) > 0) {
            return false;
        }

        $folder = new Folder();
        $folder->setMinute($minute);
        $folder->setControl($control);
        $folder->setNature(Folder::NATURE_OBSTACLE);

        if ($type === Folder::TYPE_FOLDER) {
            /** Classic Folder */
            $folder->setType(Folder::TYPE_FOLDER);
        } elseif ($type === Folder::TYPE_REFRESH) {
            /** Refresh Folder */
            $folder->setType(Folder::TYPE_REFRESH);
        } else {
            throw new NotFoundHttpException('Bad Folder type given');
        }

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
     */
    public function configureObstacleFolder(Folder $folder): Folder|bool
    {
        if ($folder->hasNatinf('33058')) {
            return false;
        }

        /** Specific Natinf */
        $natinfObstacle = $this->em->getRepository(Natinf::class)->findOneBy([
            'num' => '33058'
        ]);

        $folder->addNatinf($natinfObstacle);

        return $folder;
    }

    /**
     * Add all default element on folder
     */
    public function addChecklistToFolder(Folder $folder): Folder
    {
        if ($folder->getType() === Folder::TYPE_REFRESH) {
            $status = Checklist::STATUS_UPDATING;
        } else {
            $status = Checklist::STATUS_MINUTE;
        }

        /** Check if a standard checklist exist */
        $list = $this->em->getRepository(Checklist::class)->findOneBy([
            'status' => $status
        ]);

        /** If list exist - add list + all elements to folder */
        if ($list) {
            $elements = $this->em->getRepository(Element::class)->findActiveByChecklist($list);

            foreach ($elements as $element) {
                $folder->addElement(new ElementChecked($folder, $element));
            }
        }

        return $folder;
    }

    /**
     * Close folder and create Courier
     */
    public function closeFolder(Folder $folder): bool
    {
        if ($folder->getDateClosure()) {
            return false;
        }

        /** If the folder is edited manually we need to replace the custom tag in the edited version */
        if($folder->getEdition()?->getFolderEdited()){
            $folder->getEdition()->setFolderVersion($this->replaceCustomTags($folder->getEdition()->getFolderVersion()));
        }

        /** Replace tag added by the user to indicate part of text that can't have page break inside */
        $folder->setAscertainment($this->replaceCustomTags($folder->getAscertainment()));
        $folder->setDetails($this->replaceCustomTags($folder->getDetails()));
        $folder->setViolation($this->replaceCustomTags($folder->getViolation()));

        $folder->setDateClosure(new \Datetime('now'));
        $folder->getControl()->setIsFenced(true);

        $this->courierManager->createCourierToFolder($folder);

        try {
            $this->em->persist($folder->getControl());
            $this->em->persist($folder);
        } catch (ORMException $ORMException) {
            echo 'Error to close Obstacle folder - ' . $ORMException->getMessage();
        }

        return true;
    }

    /**
     * Purge empty element on Folder entity
     */
    public function purgeChecklist(Folder $folder): Folder
    {
        $elements = $folder->getElements();

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

        return $folder;
    }

    /**
     * This function is used to replace tags that the user had to specify which part of the text can't have a page break inside
     */
    public function replaceCustomTags($data): array|string|null
    {
        if (!$data) {
            return $data;
        }

        $avoidBreakPageTags = explode(',', $this->params->get('lucca_folder.avoid_break_page'));

        /** Search if there is custom tag in the html */
        $startOffset = strpos($data, $avoidBreakPageTags[0]);
        $endOffset = strpos($data, $avoidBreakPageTags[1]);

        /** If we find at least one of them we need to do something */
        while($startOffset || $endOffset){
            /** If we find a pair of starting and ending tags we can replace by the html elements */
            if ($startOffset && $endOffset){
                $data = substr_replace($data, '<div class="page-break-inside-avoid">', $startOffset, strlen($avoidBreakPageTags[0]) );
                $endOffset = strpos($data, $avoidBreakPageTags[1]);
                $data = substr_replace($data, '</div>', $endOffset, strlen($avoidBreakPageTags[1]) );
            }else{ /** But if we find only one tag and not a pair we need to remove it from the html */
                $data = str_replace($avoidBreakPageTags[0], "", $data);
                $data = str_replace($avoidBreakPageTags[1], "", $data);
            }

            /** Each loop we need to check again if there is still some custom tags */
            $startOffset = strpos($data, $avoidBreakPageTags[0]);
            $endOffset = strpos($data, $avoidBreakPageTags[1]);
        }

        return $data;
    }
}
