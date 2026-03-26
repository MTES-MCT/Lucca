<?php
/*
 * copyright (c) 2025-2026. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Lucca\Bundle\ParameterBundle\Entity\{Town, Intercommunal, Service, Tribunal};
use Doctrine\ORM\EntityManagerInterface;

readonly class GeoApiService
{
    private const BATCH_SIZE = 500;
    private const BASE_URL_GEO = 'https://geo.api.gouv.fr';
    private const BASE_URL_PUBLIC = 'https://etablissements-publics.api.gouv.fr/v3';

    public function __construct(
        private HttpClientInterface    $httpClient,
        private EntityManagerInterface $em
    ) {}

    /**
     * Main entry point for department data import
     */
    public function importDataForDepartment(Department $department): void
    {
        // 0. Update the department name from API
        $this->hydrateNameDepartment($department);

        // 1. Import EPCIs
        $this->importIntercommunals($department);

        // 2. Import Towns (High volume)
        $this->importTowns($department);

        // 3. Import Jurisdictions
        $this->importTribunals($department);

        // 4. Import State Services
        $this->importStateServices($department);
    }

    /**
     * Verifies that the required APIs are responsive before starting the process.
     * @throws \Exception
     */
    public function checkApisAvailability(string $code = '34'): void
    {
        $testUrls = [
            'Geo API' => self::BASE_URL_GEO . '/departements/' . $code,
            'Public Establishments API' => self::BASE_URL_PUBLIC . '/departements/' . $code . '/tgi'
        ];

        foreach ($testUrls as $name => $url) {
            try {
                $response = $this->httpClient->request('GET', $url, ['timeout' => 5]);
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception(sprintf("Error %s on %s", $response->getStatusCode(), $name));
                }
            } catch (\Exception | TransportExceptionInterface $e) {
                throw new \Exception(sprintf("API %s with url %s is not available", $name, $url), 0, $e);
            }
        }
    }

    public function hydrateNameDepartment(Department $department): void
    {
        $url = sprintf('%s/departements/%s', self::BASE_URL_GEO, $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();
        $department->setName($data['nom']);
    }

    /**
     * Import Intercommunals (EPCI) linked to the department
     */
    private function importIntercommunals(Department $department): void
    {
        $this->ensureManaged($department);

        $url = sprintf('%s/epcis?codeDepartement=%s', self::BASE_URL_GEO, $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $epcis = $response->toArray();

        foreach ($epcis as $data) {
            $interco = $this->em->getRepository(Intercommunal::class)->findOneBy(['code' => $data['code']])
                ?? new Intercommunal();

            $interco->setName($data['nom']);
            $interco->setCode($data['code']);
            $interco->setDepartment($department);
            $interco->setEnabled(true);

            if (isset($data['codeInsee'])) {
                $town = $this->em->getRepository(Town::class)->findOneBy([
                    'code' => $data['codeInsee'],
                    'department' => $department
                ]);

                if ($town) {
                    $interco->setOffice($town);
                }
            }

            $this->em->persist($interco);
        }

        $this->em->flush();
    }

    /**
     * Import Towns with batch processing to optimize memory
     */
    private function importTowns(Department $department): void
    {
        $this->ensureManaged($department);

        $url = sprintf('%s/departements/%s/communes?fields=nom,code,codesPostaux,codeEpci', self::BASE_URL_GEO, $department->getCode());
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

                if (($i % self::BATCH_SIZE) === 0) {
                    $this->flushAndClear($department);
                }
            }
        }
        $this->flushAndClear($department);
    }

    /**
     * Import Tribunals and map address properties from API
     */
    private function importTribunals(Department $department): void
    {
        $this->ensureManaged($department);

        $url = sprintf('%s/departements/%s/tgi', self::BASE_URL_PUBLIC, $department->getCode());
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();

        if (!isset($data['features'])) return;

        foreach ($data['features'] as $feature) {
            $props = $feature['properties'];

            $tribunal = $this->em->getRepository(Tribunal::class)->findOneBy([
                'name' => $props['nom'],
                'department' => $department
            ]) ?? new Tribunal();

            $tribunal->setName($props['nom']);
            $tribunal->setCode($props['codeInsee']);
            $tribunal->setDepartment($department);
            $tribunal->setEnabled(true);
            $tribunal->setCountry('FR');

            // Find the Town entity using the codeInsee to link as Office
            if (isset($props['codeInsee'])) {
                $town = $this->em->getRepository(Town::class)->findOneBy([
                    'code' => $props['codeInsee'],
                    'department' => $department
                ]);

                if ($town) {
                    $tribunal->setOffice($town);
                }
            }

            // Set address if exists in API properties
            if (!empty($props['adresses'])) {
                $mainAddress = $props['adresses'][0];
                $streetAddress = isset($mainAddress['lignes']) ? implode(', ', $mainAddress['lignes']) : null;
                $tribunal->setAddress(mb_substr($streetAddress, 0, 80));
                $tribunal->setZipCode($mainAddress['codePostal'] ?? null);
                $tribunal->setCity($mainAddress['commune'] ?? null);
            }

            $this->em->persist($tribunal);
        }

        $this->em->flush();
    }

    /**
     * Import State Services (Gendarmerie, DDTM, etc.)
     */
    private function importStateServices(Department $department): void
    {
        $this->ensureManaged($department);

        // List of pivot codes for State Services
        // You can add more from: https://api.gouv.fr/les-api/api-etablissements-publics
        $pivots = ['gendarmerie', 'ddt', 'prefecture'];

        foreach ($pivots as $pivot) {
            $url = sprintf('%s/departements/%s/%s', self::BASE_URL_PUBLIC, $department->getCode(), $pivot);

            try {
                $response = $this->httpClient->request('GET', $url);
                $data = $response->toArray();
            } catch (\Exception $e) {
                // Some departments might not have specific services, we skip silently
                continue;
            }

            if (!isset($data['features'])) continue;

            foreach ($data['features'] as $feature) {
                $props = $feature['properties'];

                // Check if service already exists for this department
                $service = $this->em->getRepository(Service::class)->findOneBy([
                    'name' => $props['nom'],
                    'department' => $department
                ]) ?? new Service();

                $service->setName(mb_substr($props['nom'], 0, 100));
                $service->setCode($props['codeInsee'] ?? $department->getCode());
                $service->setDepartment($department);
                $service->setEnabled(true);

                // Link to Town (office) if codeInsee is available
                if (isset($props['codeInsee'])) {
                    $town = $this->em->getRepository(Town::class)->findOneBy([
                        'code' => $props['codeInsee'],
                        'department' => $department
                    ]);

                    if ($town) {
                        $service->setOffice($town);
                    }
                }

                $this->em->persist($service);
            }
        }

        $this->em->flush();
    }

    /**
     * Ensure the department is managed by the EntityManager (prevents detached entity errors)
     */
    private function ensureManaged(Department &$department): void
    {
        if (!$this->em->contains($department)) {
            $department = $this->em->find(Department::class, $department->getId());
        }
    }

    /**
     * Flush changes and clear EntityManager to free memory
     * Then re-fetch/re-attach the department entity
     */
    private function flushAndClear(Department &$department): void
    {
        $this->em->flush();
        $this->em->clear();

        // Re-fetch department from DB as it was detached by clear()
        $department = $this->em->find(Department::class, $department->getId());
    }
}
