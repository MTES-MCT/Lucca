<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\FolderBundle\Entity\Natinf;

readonly class NatinfService
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
        $natinfs = $this->readNatinfs();

        $this->insertWithoutParent($department, $natinfs);
        $this->updateParents($department, $natinfs);
    }

    public function readNatinfs(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/FolderBundle/Data/natinfs.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flash.natinf.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flash.natinf.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        return $data;
    }

    private function insertWithoutParent(Department $department, array $natinfs): void
    {
        // Proceed data with a batch to avoid memory issues
        $natinfChunks = array_chunk($natinfs, 40);
        foreach ($natinfChunks as $natinfChunk) {
            // Re-attach the department entity if detached by a clean
            $department = $this->em->getReference(Department::class, $department->getId());
            foreach ($natinfChunk as $natinf) {
                $newNatinf = new Natinf();
                $newNatinf->setNum($natinf['num']);
                $newNatinf->setQualification($natinf['qualification']);
                $newNatinf->setDefinedBy($natinf['definedBy']);
                $newNatinf->setRepressedBy($natinf['repressedBy']);
                $newNatinf->setDepartment($department);

                $this->em->persist($newNatinf);
            }

            $this->em->flush();
            $this->em->clear(); // Detaches all objects from Doctrine!
        }
    }

    private function updateParents(Department $department, array $natinfs): void
    {
        $dbNatinfs = $this->em->getRepository(Natinf::class)->getIdentifiers($department);
        foreach ($natinfs as $natinf) {
            if (empty($natinf['parent_num'])) {
                continue;
            }

            $childNatinf = array_find($dbNatinfs, fn($n) => $n->getNum() === (int)$natinf['num']);
            $parentNatinf = array_find($dbNatinfs, fn($n) => $n->getNum() === (int)$natinf['parent_num']);

            $childNatinf->setParent($parentNatinf);

            $this->em->persist($childNatinf);
        }

        $this->em->flush();
    }
}
