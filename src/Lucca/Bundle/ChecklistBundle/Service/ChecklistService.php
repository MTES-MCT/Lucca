<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\ChecklistBundle\Entity\Checklist;
use Lucca\Bundle\ChecklistBundle\Entity\Element;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\FolderBundle\Entity\Natinf;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

readonly class ChecklistService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    )
    {
    }

    public function createForDepartment(Department $department): void
    {
        $checklists = $this->readChecklists();

        $this->insertFromData($department, $checklists);
    }

    public function readChecklists(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/ChecklistBundle/Data/checklists.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flashes.checklist.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flashes.checklist.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        return $data;
    }

    private function insertFromData(Department $department, array $checklists): void
    {
        foreach ($checklists as $checklist) {
            $newChecklist = new Checklist();
            $newChecklist->setName($checklist['name']);
            $newChecklist->setStatus($checklist['status']);
            $newChecklist->setDepartment($department);

            $this->em->persist($newChecklist);

            foreach ($checklists['children'] as $element) {
                $newElement = new Element();
                $newElement->setName($element['name']);
                $newElement->setPosition($element['position']);
                $newElement->setChecklist($newChecklist);

                $this->em->persist($newElement);
            }
        }

        $this->em->flush();
        $this->em->clear();
    }
}
