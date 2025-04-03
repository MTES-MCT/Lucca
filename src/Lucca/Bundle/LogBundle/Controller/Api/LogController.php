<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\LogBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\LogBundle\Entity\Log;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/log')]
#[IsGranted('ROLE_ADMIN')]
class LogController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    #[Route(path: '/get-list-datatable', name: 'lucca_log_api_list_datatable', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getListDatatableAction(Request $request): JsonResponse
    {
        /** Get other filters */
        $payload = $request->getPayload()->all();
        $columns = $payload['columns'];
        $order = $payload['order'];

        $orderColumn = $columns[$order[0]['column']]['name'];
        $orderDirection = $order[0]['dir'];

        unset($payload['draw'], $payload['columns'], $payload['order'], $payload['search']);

        ['count' => $count, 'data' => $data, 'recordsFiltered' => $recordsFiltered] = $this->em->getRepository(Log::class)
            ->getDatatableData($orderColumn, $orderDirection, ...$payload);

        foreach ($data as $key => $d) {
            $data[$key]['status'] = $this->translator->trans($d['status'], [], 'LogBundle');
        }

        return new JsonResponse(array(
            'data' => $data,
            'recordsTotal' => $count,
            'recordsFiltered' => $recordsFiltered,
        ));
    }
}
