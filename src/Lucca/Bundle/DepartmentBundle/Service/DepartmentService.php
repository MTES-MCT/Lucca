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

    public function createTownsFromFile(UploadedFile $file, Department $department): void
    {
        $towns = $this->readTowns($file);

        // Insert intercommunals before town to link them more easily
        $intercommunals = $this->insertIntercommunals($department, $towns);
        $this->insertTowns($department, $towns, $intercommunals);
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

    private function insertTowns(Department $department, array $towns, array $intercommunals): void {
        // Proceed data with a batch to avoid memory issues
        $townChunks = array_chunk($towns, 40);
        foreach ($townChunks as $townChunk) {
            // Re-attach the department entity if detached by a clean
            $department = $this->em->getReference(Department::class, $department->getId());

            foreach ($townChunk as $town) {
                if (empty($town[self::TOWN_INSEE]) || empty($town[self::TOWN_ZIP]) || empty($town[self::TOWN_NAME])) {
                    continue; // Skip empty rows
                }

                $newTown = new Town();
                $newTown->setCode((string)$town[self::TOWN_INSEE]);
                $newTown->setZipcode((string)$town[self::TOWN_ZIP]);
                $newTown->setName($town[self::TOWN_NAME]);
                $newTown->setOffice($town[self::TOWN_NAME]);
                $newTown->setDepartment($department);

                if (!empty($town[self::INTER_INSEE]) && !empty($towns[self::INTER_NAME])) {
                    $interco = $this->em->getReference(Intercommunal::class, $intercommunals[$town[self::INTER_INSEE]]->getId());
                    $newTown->setIntercommunal($interco);
                }

                $this->em->persist($newTown);
            }

            $this->em->flush();
            $this->em->clear(); // Detaches all objects from Doctrine!
        }
    }

    private function insertIntercommunals(Department $department, array $towns): array
    {
        $intercommunalGrouped = [];
        $persistedIntercommunals = [];

        foreach ($towns as $town) {
            if (!empty($town[self::INTER_INSEE]) && !empty($town[self::INTER_NAME])) {
                $intercommunalGrouped[$town[self::INTER_INSEE]] = $town[self::INTER_NAME];
            }
        }

        // Re-attach the department entity if detached by a clean
        $department = $this->em->getReference(Department::class, $department->getId());
        foreach ($intercommunalGrouped as $code => $intercommunal) {
            $newIntercommunal = new Intercommunal();
            $newIntercommunal->setCode((string)$code);
            $newIntercommunal->setName($intercommunal);
            $newIntercommunal->setDepartment($department);

            $this->em->persist($newIntercommunal);
            $persistedIntercommunals[$newIntercommunal->getCode()] = $newIntercommunal;
        }

        $this->em->flush();
        $this->em->clear();

        return $persistedIntercommunals;
    }
}
