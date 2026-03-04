<?php

/*
 * Copyright (c) 2025-2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\FolderBundle\Entity\Tag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

readonly class TagService
{
    private const BATCH_SIZE = 100;

    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {}

    /**
     * Create tags for a specific department
     */
    public function createForDepartment(Department $department): void
    {
        $tags = $this->readTags();

        if ($tags) {
            $this->insertTags($department, $tags);
        }
    }

    /**
     * Read tags from the JSON data file
     */
    public function readTags(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/FolderBundle/Data/tags.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flash.tag.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flash.tag.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        return $data;
    }

    /**
     * Insert tags into database using batch processing
     */
    private function insertTags(Department $department, array $tags): void
    {
        // 1. On s'assure que le département est bien "attaché"
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        $i = 0;
        foreach ($tags as $tagData) {
            // 2. RECHERCHE D'EXISTENCE (Clé unique : num + department)
            // On cherche si ce tag existe déjà pour éviter le crash SQL
            $tag = $this->em->getRepository(Tag::class)->findOneBy([
                'num' => $tagData['num'],
                'department' => $department
            ]) ?? new Tag();

            $tag->setNum($tagData['num']);
            $tag->setName($tagData['name']);
            $tag->setCategory($tagData['category']);
            $tag->setDepartment($department);
            $tag->setEnabled(true);

            $this->em->persist($tag);
            $i++;

            if (($i % self::BATCH_SIZE) === 0) {
                $this->flushAndClear($department);
            }
        }

        $this->flushAndClear($department);
    }

    /**
     * Flush pending changes, clear the Identity Map and re-attach the main department entity
     */
    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();

        // Re-fetch the department as it becomes detached after em->clear()
        $department = $this->em->find(Department::class, $department->getId());
    }
}
