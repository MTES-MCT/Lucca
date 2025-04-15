<?php

namespace Lucca\Bundle\DepartmentBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ParameterBundle\Entity\{Intercommunal, Town};

readonly class DepartmentService
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function createTownsFromFile(UploadedFile $file, Department $department): void
    {
        $rows = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        // Proceed data with a batch to avoid memory issues
        $batchSize = 40;
        for ($i = 0; $i < count($rows); ++$i) {
            if (empty($rows[$i][0]) || empty($rows[$i][1])) {
                continue; // Skip empty rows
            }

            $town = new Town();
            $town->setCode($rows[$i][0]);
            $town->setName($rows[$i][1]);

            if (!empty($rows[$i][2]) && !empty($rows[$i][3])) {
                $intercommunal = new Intercommunal();
                $intercommunal->setCode($rows[$i][2]);
                $intercommunal->setName($rows[$i][2]);
                $intercommunal->setDepartment($department);
                $town->setIntercommunal($intercommunal);
                $this->em->persist($intercommunal);
            }

            $this->em->persist($town);
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->em->flush(); // Persist objects that did not make up an entire batch
        $this->em->clear();
    }
}
