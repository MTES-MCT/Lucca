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
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\FolderBundle\Entity\Proposal;
use Lucca\Bundle\FolderBundle\Entity\Tag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ProposalService
{
    private const BATCH_SIZE = 100;

    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {}

    /**
     * Create proposals for a specific department
     */
    public function createForDepartment(Department $department): void
    {
        $proposals = $this->readProposals();

        if ($proposals) {
            $this->insertProposals($department, $proposals);
        }
    }

    /**
     * Read proposals from the JSON data file
     */
    public function readProposals(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/FolderBundle/Data/proposals.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flash.proposal.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flash.proposal.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        return $data;
    }

    /**
     * Insert proposals using batch processing
     */
    private function insertProposals(Department $department, array $proposals): void
    {
        // Ensure the department is managed
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        $i = 0;
        foreach ($proposals as $data) {
            // Find the related tag for this department
            $tag = $this->em->getRepository(Tag::class)->findOneBy([
                'department' => $department,
                'num' => $data['tag_num']
            ]);

            if (!$tag) {
                // Skip or log if the tag is missing to avoid FK errors
                continue;
            }

            // Check if proposal already exists to prevent duplicates
            $proposal = $this->em->getRepository(Proposal::class)->findOneBy([
                'sentence' => $data['sentence'],
                'department' => $department,
                'tag' => $tag
            ]) ?? new Proposal();

            $proposal->setSentence($data['sentence']);
            $proposal->setTag($tag);
            $proposal->setDepartment($department);
            $proposal->setEnabled(true);

            $this->em->persist($proposal);
            $i++;

            if (($i % self::BATCH_SIZE) === 0) {
                $this->flushAndClear($department);
            }
        }

        $this->flushAndClear($department);
    }

    /**
     * Persist changes and refresh the department entity
     */
    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();

        // Essential: Re-fetch the department after clearing the Identity Map
        $department = $this->em->find(Department::class, $department->getId());
    }
}
