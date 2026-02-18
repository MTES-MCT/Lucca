<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\CoreBundle\Twig\BooleanExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/adherent')]
#[IsGranted('ROLE_ADMIN')]
class AdherentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BooleanExtension $booleanExtension
    )
    {
    }

    /**
     * list of adherents for datatable on lazy loading
     */
    #[Route(path: '/datatable-list', name: 'lucca_adherent_api_list_datatable', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function datatableSearchAdherentsAction(Request $request): JsonResponse
    {
        $params = $request->request->all();

        // call repository method
        $result = $this->entityManager->getRepository(Adherent::class)
            ->searchAdherentsForDatatable($params);

        // format final result
        $result['data'] = array_map(function($item) {
            return [
                'officialName' => trim(($item['firstname'] ?? '') . ' ' . ($item['name'] ?? '')),
                'email'        => $item['user']['email'] ?? '-',
                'phone'        => $item['phone'] ?? $item['mobile'] ?? '-',
                'location'     => $item['service']['name'] ?? $item['intercommunal']['name'] ?? $item['town']['name'] ?? '-',
                'address'      => implode(' ', [$item['address'] ?? '', $item['zipCode'] ?? '', $item['city'] ?? '']),
                'username'     => $item['user']['username'] ?? '-',
                'groups'       => !empty($item['user']['groups']) ? implode('<br>', array_column($item['user']['groups'], 'name')) : '',
                'logo'         => $this->booleanExtension->booleanFilter(isset($item['logo'])),
                'actions'      => $this->renderView('@LuccaAdherent/Adherent/_actions.html.twig', [
                    'id' => $item['id'],
                    'enabled' => $item['enabled']
                ])
            ];
        }, $result['data']);

        return new JsonResponse($result);
    }

}
