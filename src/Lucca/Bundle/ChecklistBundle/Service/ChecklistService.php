<?php

/*
 * Copyright (c) 2025-2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ChecklistBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\ChecklistBundle\Entity\{Checklist, Element};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

readonly class ChecklistService
{
    private const BATCH_SIZE = 50;

    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {}

    public function createForDepartment(Department $department): void
    {
        $checklists = $this->readChecklists();
        if ($checklists) {
            $this->insertFromData($department, $checklists);
        }
    }

    public function readChecklists(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/ChecklistBundle/Data/checklists.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flash.checklist.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flash.checklist.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        return $data;
    }

    private function insertFromData(Department $department, array $checklists): void
    {
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        foreach ($checklists as $index => $data) {
            $checklist = $this->em->getRepository(Checklist::class)->findOneBy([
                'name' => $data['name'],
                'department' => $department
            ]) ?? new Checklist();

            $checklist->setName($data['name']);
            $checklist->setStatus($data['status']);
            $checklist->setDepartment($department);
            $checklist->setEnabled(true);

            $this->em->persist($checklist);

            foreach ($data['children'] as $elementData) {
                // Find or create element to avoid duplicates inside checklist
                $element = $this->em->getRepository(Element::class)->findOneBy([
                    'name' => $elementData['name'],
                    'checklist' => $checklist
                ]) ?? new Element();

                $element->setName($elementData['name']);
                $element->setPosition($elementData['position']);
                $element->setChecklist($checklist);
                $element->setEnabled(true);

                $this->em->persist($element);
            }

            if ((($index + 1) % self::BATCH_SIZE) === 0) {
                $this->flushAndClear($department);
            }
        }
        $this->flushAndClear($department);
    }

    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();
        $department = $this->em->find(Department::class, $department->getId());
    }
}
