<?php
/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */
namespace Lucca\Bundle\DepartmentBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal, Service, Tag};
use Doctrine\ORM\EntityManagerInterface;

readonly class GeoApiService
{
    private const BATCH_SIZE = 500;

    public function __construct(
        private HttpClientInterface    $httpClient,
        private EntityManagerInterface $em
    ) {}

    public function importDataForDepartment(Department $department): void
    {
        // 1. Import EPCIs
        $this->importIntercommunals($department);

        // 2. Import Towns (High volume)
        $this->importTowns($department);

        // 3. Import Jurisdictions
        $this->importTribunals($department);
    }

    public function hydrateNameDepartment(Department $department): void
    {
        $url = sprintf('https://geo.api.gouv.fr/departements/%s', $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();
        $department->setName($data['nom']);
    }

    private function importIntercommunals(Department $department): void
    {
        $url = sprintf('https://geo.api.gouv.fr/epcis?codeDepartement=%s', $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $epcis = $response->toArray();

        foreach ($epcis as $data) {
            $interco = $this->em->getRepository(Intercommunal::class)->findOneBy(['code' => $data['code']])
                ?? new Intercommunal();

            $interco->setName($data['nom']);
            $interco->setCode($data['code']);
            $interco->setDepartment($department);
            $interco->setEnabled(true);

            $this->em->persist($interco);
        }

        $this->em->flush();
    }

    private function importTowns(Department $department): void
    {
        $url = sprintf('https://geo.api.gouv.fr/departements/%s/communes?fields=nom,code,codesPostaux,codeEpci', $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $towns = $response->toArray();

        $i = 0;
        foreach ($towns as $data) {
            foreach ($data['codesPostaux'] as $zip) {
                // Check if town already exists to avoid unique constraint violations
                $town = $this->em->getRepository(Town::class)->findOneBy([
                    'code' => $data['code'],
                    'zipcode' => $zip
                ]) ?? new Town();

                $town->setName($data['nom']);
                $town->setCode($data['code']);
                $town->setOffice($data['nom']);
                $town->setZipcode($zip);
                $town->setDepartment($department);
                $town->setEnabled(true);

                if (isset($data['codeEpci'])) {
                    $interco = $this->em->getRepository(Intercommunal::class)->findOneBy(['code' => $data['codeEpci']]);
                    if ($interco) {
                        $town->setIntercommunal($interco);
                    }
                }

                $this->em->persist($town);
                $i++;

                // Batch processing to optimize memory and performance
                if (($i % self::BATCH_SIZE) === 0) {
                    $this->flushAndClear($department);
                }
            }
        }
        $this->flushAndClear($department);
    }

    private function importTribunals(Department $department): void
    {
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }

        $url = sprintf('https://etablissements-publics.api.gouv.fr/v3/departements/%s/tgi', $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();

        if (!isset($data['features'])) return;

        foreach ($data['features'] as $feature) {
            $props = $feature['properties'];

            $service = $this->em->getRepository(Service::class)->findOneBy([
                'name' => $props['nom'],
                'department' => $department
            ]) ?? new Service();

            $service->setName($props['nom']);
            $service->setCode($props['codeInsee']);
            $service->setDepartment($department);
            $service->setEnabled(true);

            // FIX: Find the Town entity using the codeInsee
            if (isset($props['codeInsee'])) {
                $town = $this->em->getRepository(Town::class)->findOneBy([
                    'code' => $props['codeInsee'],
                    'department' => $department
                ]);

                // We must pass the Town OBJECT, not the name string
                if ($town) {
                    $service->setOffice($town);
                }
            }

            $this->em->persist($service);
        }

        $this->em->flush();
    }

    /**
     * Flush changes and clear EntityManager to free memory
     * Then re-attach the department entity
     */
    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();

        // Re-fetch department from DB as it was detached by clear()
        $department = $this->em->find(Department::class, $department->getId());
    }
}
