<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\FolderBundle\Entity\Natinf;
use Lucca\Bundle\DepartmentBundle\Entity\Department;

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
            $message = $this->translator->trans('flashes.natinf.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flashes.natinf.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);

            return null;
        }

        return $data;
    }

    private function insertWithoutParent(Department $department, array $natinfs): void
    {
        // Proceed data with a batch to avoid memory issues
        $batchSize = 40;
        for ($i = 0; $i < count($natinfs); ++$i) {
            $natinf = new Natinf();
            $natinf->setNum($natinfs['num']);
            $natinf->setQualification($natinf['qualification']);
            $natinf->setDefinedBy($natinf['definedBy']);
            $natinf->setRepressedBy($natinf['repressedBy']);
            $natinf->setDepartment($department);

            $this->em->persist($natinf);
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush(); // Persist objects that did not make up an entire batch
        $this->em->clear();
    }

    private function updateParents(Department $department, array $natinfs): void
    {
        $dbNatinfs = $this->em->getRepository(Natinf::class)->getIdentifiers($department);

        // Proceed data with a batch to avoid memory issues
        $batchSize = 40;
        for ($i = 0; $i < count($natinfs); ++$i) {
            if (empty($natinf['parent_num'])) {
                continue;
            }

            $dbNatinf = array_find($dbNatinfs, fn ($dbNatinf) => $dbNatinf['num'] === $natinf['parent_num']);
            if (!$dbNatinf) {
                continue;
            }

            $natinf = new Natinf();
            $natinf->setParent($dbNatinf['num']);

            $this->em->persist($natinf);
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush(); // Persist objects that did not make up an entire batch
        $this->em->clear();
    }
}
