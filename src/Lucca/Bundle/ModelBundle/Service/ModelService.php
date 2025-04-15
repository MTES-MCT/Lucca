<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\ModelBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ModelBundle\Entity\{Margin, Model, Page};

readonly class ModelService
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
        $models = $this->readModels();

        $this->insertFromData($department, $models);
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
        // Re-attach the department entity if detached by a clean
        $department = $this->em->getReference(Department::class, $department->getId());

        foreach ($models as $model) {
            $newModel = new Model();
            $newModel->setName($model['name']);
            $newModel->setType(Model::TYPE_ORIGIN);
            $newModel->setLayout($model['layout']);
            $newModel->setDocuments(json_decode($model['documents']));
            $newModel->setDepartment($department);

            $this->em->persist($newModel);

            foreach (['recto', 'verso'] as $side) {
                if (!$model[$side]) continue;

                $newSide = new Page();
                $newSide->setMarginUnit($model[$side]['marginUnit']);
                $newSide->setHeaderSize($model[$side]['headerSize']);
                $newSide->setFooterSize($model[$side]['footerSize']);
                $newSide->setLeftSize($model[$side]['leftSize']);
                $newSide->setRightSize($model[$side]['rightSize']);
                $newSide->setDepartment($department);

                $setter = 'set' . ucwords($side);
                $newModel->$setter($newSide);

                $this->em->persist($newSide);

                foreach ($model[$side]['margins'] as $margin) {
                    $newMargin = new Margin();
                    $newMargin->setPosition($margin['position']);
                    $newMargin->setHeight($margin['height']);
                    $newMargin->setWidth($margin['width']);
                    $newMargin->setDepartment($department);

                    $choice = explode('.', $margin['position']);
                    $position = array_pop($choice);

                    $setter = 'setMargin' . ucwords($position);
                    $newSide->$setter($newMargin);

                    $this->em->persist($newSide);
                }
            }
        }

        $this->em->flush();
        $this->em->clear();
    }
}
