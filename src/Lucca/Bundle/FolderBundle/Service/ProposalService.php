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
use Lucca\Bundle\FolderBundle\Entity\Proposal;

readonly class ProposalService
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
        $proposals = $this->readProposals();

        $this->insertProposal($department, $proposals);
    }


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

    private function insertProposal(Department $department, array $proposals): void
    {
        // Proceed data with a batch to avoid memory issues
        $proposalChunks = array_chunk($proposals, 40);
        foreach ($proposalChunks as $proposalChunk) {


            // Re-attach the department entity if detached by a clean
            $department = $this->em->getReference(Department::class, $department->getId());
            foreach ($proposalChunk as $proposal) {
                $tag = $this->em->getRepository(Tag::class)->findOneBy(['department' => $department->getId(), 'num' => $proposal['tag_num']]);

                $newProposal = new Proposal();
                $newProposal->setSentence($proposal['sentence']);
                $newProposal->setTag($tag);
                $newProposal->setDepartment($department);

                $this->em->persist($newProposal);
            }

            $this->em->flush();
            $this->em->clear(); // Detaches all objects from Doctrine!
        }
    }
}
