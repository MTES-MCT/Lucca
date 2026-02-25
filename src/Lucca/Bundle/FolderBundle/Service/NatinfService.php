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
use Lucca\Bundle\FolderBundle\Entity\Natinf;

readonly class NatinfService
{
    private const BATCH_SIZE = 200;

    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {}

    public function createForDepartment(Department $department): void
    {
        $natinfs = $this->readNatinfs();
        if (!$natinfs) return;

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
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        $i = 0;
        foreach ($natinfs as $data) {
            $natinf = $this->em->getRepository(Natinf::class)->findOneBy([
                'num' => $data['num'],
                'department' => $department
            ]) ?? new Natinf();

            $natinf->setNum($data['num']);
            $natinf->setQualification($data['qualification']);
            $natinf->setDefinedBy($data['definedBy']);
            $natinf->setRepressedBy($data['repressedBy']);
            $natinf->setDepartment($department);
            $natinf->setEnabled(true);

            // Fetch and attach tags
            $tags = $this->em->getRepository(Tag::class)->findAllByDepartmentAndNums($department, $data['tags_num_link']);
            foreach ($tags as $tag) {
                if (!$natinf->getTags()->contains($tag)) {
                    $natinf->addTag($tag);
                }
            }

            $this->em->persist($natinf);
            $i++;

            if (($i % self::BATCH_SIZE) === 0) {
                $this->flushAndClear($department);
            }
        }
        $this->flushAndClear($department);
    }

    private function updateParents(Department $department, array $natinfs): void
    {
        // Re-fetch managed department
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        $dbNatinfs = $this->em->getRepository(Natinf::class)->findBy(['department' => $department]);

        $i = 0;
        foreach ($natinfs as $data) {
            if (empty($data['parent_num'])) continue;

            $child = array_find($dbNatinfs, fn($n) => $n->getNum() === (int)$data['num']);
            $parent = array_find($dbNatinfs, fn($n) => $n->getNum() === (int)$data['parent_num']);

            if ($child && $parent) {
                $child->setParent($parent);
                $this->em->persist($child);
                $i++;
            }

            if (($i % self::BATCH_SIZE) === 0) {
                $this->em->flush(); // No clear here to keep references for the loop
            }
        }
        $this->em->flush();
    }

    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();
        $department = $this->em->find(Department::class, $department->getId());
    }
}
