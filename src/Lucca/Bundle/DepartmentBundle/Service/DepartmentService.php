<?php

namespace Lucca\Bundle\DepartmentBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Town};

readonly class DepartmentService
{
    const TOWN_INSEE = 0;
    const TOWN_ZIP = 1;
    const TOWN_NAME = 2;
    const INTER_INSEE = 3;
    const INTER_NAME = 4;

    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function createOrUpdateTownsFromFile(UploadedFile $file, Department $department): void
    {
        $towns = $this->readTowns($file);

        // Insert intercommunals before town to link them more easily
        $intercommunals = $this->insertOrUpdateIntercommunals($department, $towns);
        $this->insertOrUpdateTowns($department, $towns, $intercommunals);
    }

    public function readTowns(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getPathname(), 'r');
        if ($handle !== false) {
            // Skip UTF-8 BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle); // Go back to the beginning if no BOM
            }

            while (($data = fgetcsv($handle)) !== false) {
                // Convert each field to UTF-8 if needed
                $convertedRow = array_map(function ($value) {
                    $encoding = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                    return $encoding !== 'UTF-8' ? mb_convert_encoding($value, 'UTF-8', $encoding) : $value;
                }, $data);

                $rows[] = $convertedRow;
            }
            fclose($handle);
        }

        // Remove the first row (header)
        if (count($rows) > 0) {
            array_shift($rows);
        }

        return $rows;
    }

    private function insertOrUpdateTowns(Department $department, array $towns, array $intercommunals): void {
        // ⚡ Load all existing towns once (avoid duplicates & lots of queries)
        $departmentRef = $this->em->getReference(Department::class, $department->getId());
        $existingTowns = $this->em->getRepository(Town::class)->findBy([
            'department' => $departmentRef
        ]);

        $townMap = [];
        foreach ($existingTowns as $t) {
            $townMap[$t->getCode()] = $t;
        }

        $townChunks = array_chunk($towns, 40);

        foreach ($townChunks as $townChunk) {
            foreach ($townChunk as $town) {
                if (empty($town[self::TOWN_INSEE]) || empty($town[self::TOWN_ZIP]) || empty($town[self::TOWN_NAME])) {
                    continue; // Skip empty rows
                }

                $code = (string)$town[self::TOWN_INSEE];

                // Update or create
                if (isset($townMap[$code])) {
                    $townEntity = $townMap[$code];
                } else {
                    $townEntity = new Town();
                    $townEntity->setCode($code);
                    $townEntity->setDepartment($departmentRef);
                    $this->em->persist($townEntity);
                    $townMap[$code] = $townEntity;
                }

                // Update fields
                $townEntity->setZipcode((string)$town[self::TOWN_ZIP]);
                $townEntity->setName($town[self::TOWN_NAME]);
                $townEntity->setOffice($town[self::TOWN_NAME]);

                // Link intercommunal if exists
                if (!empty($town[self::INTER_INSEE]) && !empty($town[self::INTER_NAME])) {
                    if (isset($intercommunals[$town[self::INTER_INSEE]])) {
                        $intercoId = $intercommunals[$town[self::INTER_INSEE]];
                        $interco = $this->em->getReference(Intercommunal::class, $intercoId);
                        $townEntity->setIntercommunal($interco);
                    }
                }
            }

            $this->em->flush();

            // ⚠️ After clear, we must reattach department and reload townMap
            $departmentRef = $this->em->getReference(Department::class, $department->getId());
            $existingTowns = $this->em->getRepository(Town::class)->findBy([
                'department' => $departmentRef
            ]);

            $townMap = [];
            foreach ($existingTowns as $t) {
                $townMap[$t->getCode()] = $t;
            }
        }
    }

    private function insertOrUpdateIntercommunals(Department $department, array $towns): array
    {
        $intercommunalGrouped = [];
        $persistedIntercommunals = [];

        foreach ($towns as $town) {
            if (!empty($town[self::INTER_INSEE]) && !empty($town[self::INTER_NAME])) {
                $intercommunalGrouped[$town[self::INTER_INSEE]] = $town[self::INTER_NAME];
            }
        }

        $departmentRef = $this->em->getReference(Department::class, $department->getId());

        // ⚡ Load all existing intercos once
        $existingIntercos = $this->em->getRepository(Intercommunal::class)->findBy([
            'department' => $departmentRef
        ]);

        $intercoMap = [];
        foreach ($existingIntercos as $inter) {
            $intercoMap[$inter->getCode()] = $inter;
        }

        foreach ($intercommunalGrouped as $code => $intercommunalName) {

            if (isset($intercoMap[$code])) {
                $intercoMap[$code]->setName($intercommunalName);
                $persistedIntercommunals[$code] = $intercoMap[$code]->getId();
            } else {
                $newIntercommunal = new Intercommunal();
                $newIntercommunal->setCode((string)$code);
                $newIntercommunal->setName($intercommunalName);
                $newIntercommunal->setDepartment($departmentRef);

                $this->em->persist($newIntercommunal);
                $persistedIntercommunals[$code] = $newIntercommunal; // temporary
            }
        }

        $this->em->flush();

        // Convert persisted objects to IDs (because they are detached after clear)
        foreach ($persistedIntercommunals as $code => $interco) {
            if ($interco instanceof Intercommunal) {
                $persistedIntercommunals[$code] = $interco->getId();
            }
        }

        return $persistedIntercommunals;
    }
}
