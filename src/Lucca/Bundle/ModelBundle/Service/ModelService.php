<?php

/*
 * Copyright (c) 2025-2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Entity\{Margin, Model, Page};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ModelService
{
    private const BATCH_SIZE = 25;

    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {}

    public function createForDepartment(Department $department): void
    {
        $models = $this->readModels();
        if ($models) {
            $this->insertFromData($department, $models);
        }
    }

    public function readModels(): ?array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/src/Lucca/Bundle/ModelBundle/Data/models.json';

        if (!file_exists($filePath)) {
            $message = $this->translator->trans('flash.model.data.notFound', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $message = $this->translator->trans('flash.model.data.invalidJSON', [], 'FlashMessages');
            $this->requestStack->getSession()->getFlashBag()->add('error', $message);
            return null;
        }

        return $data;
    }

    private function insertFromData(Department $department, array $models): void
    {
        // Ensure the department is managed by the current EntityManager
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        foreach ($models as $index => $data) {
            // Find existing model or create a new one
            $model = $this->em->getRepository(Model::class)->findOneBy([
                'name' => $data['name'],
                'department' => $department
            ]) ?? new Model();

            $model->setName($data['name']);
            $model->setType(Model::TYPE_ORIGIN);
            $model->setLayout($data['layout']);
            $model->setDocuments(json_decode($data['documents']));
            $model->setDepartment($department);
            $model->setEnabled(true);

            foreach (['recto', 'verso'] as $side) {
                if (empty($data[$side])) {
                    continue;
                }

                $sideData = $data[$side];

                // DYNAMIC FIX: Use the variable getter to avoid "uninitialized property" crash
                $getter = 'get' . ucwords($side);
                $page = null;

                // Only call the getter if the model already exists in DB
                if ($model->getId() !== null) {
                    try {
                        $page = $model->$getter();
                    } catch (\Error $e) {
                        // Fallback if property is typed but not initialized
                        $page = null;
                    }
                }

                $page = $page ?? new Page();
                $page->setMarginUnit($sideData['marginUnit']);
                $page->setHeaderSize($sideData['headerSize']);
                $page->setFooterSize($sideData['footerSize']);
                $page->setLeftSize($sideData['leftSize']);
                $page->setRightSize($sideData['rightSize']);
                $page->setDepartment($department);

                // Link the page to the model (setRecto or setVerso)
                $setter = 'set' . ucwords($side);
                $model->$setter($page);

                $this->em->persist($page);

                // Handle Margins for the current page
                foreach ($sideData['margins'] as $marginData) {
                    $choice = explode('.', $marginData['position']);
                    $posName = array_pop($choice); // e.g., 'top', 'bottom', 'left', 'right'

                    $marginGetter = 'getMargin' . ucwords($posName);

                    // Safety check for margin initialization
                    $margin = null;
                    if ($page->getId() !== null) {
                        try {
                            $margin = $page->$marginGetter();
                        } catch (\Error $e) {
                            $margin = null;
                        }
                    }

                    $margin = $margin ?? new Margin();
                    $margin->setPosition($marginData['position']);
                    $margin->setHeight($marginData['height']);
                    $margin->setWidth($marginData['width']);
                    $margin->setDepartment($department);

                    $this->em->persist($margin);

                    $linkSetter = 'setMargin' . ucwords($posName);
                    $page->$linkSetter($margin);
                }

                // Persist page after margins are linked
                $this->em->persist($page);
            }

            // Final persist for the model
            $this->em->persist($model);

            // Batch processing to clear memory
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
