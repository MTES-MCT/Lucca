<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\Controller\RestApi;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\CoreBundle\Service\RestService;
use Lucca\Bundle\FolderBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_LUCCA')]
#[Route(path: '/folder')]
class FolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RestService            $restService,
        private readonly RouterInterface        $router,
        private readonly TranslatorInterface   $translator,
    )
    {
    }

    /**
     * Lists all Folder entities.
     */
    #[Route(path: '/', name: 'lucca_folder_rest_api_folder_list', methods: ['GET'])]
    #[IsGranted('ROLE_LUCCA')]
    public function newAction(
        Request $request
    ): JsonResponse
    {
        try {
            $filters = $this->restService->prepareFilters(
                $request->query->all(),
                [
                    'INSEE',
                    'townName',
                    'plotCode',
                    'number',
                    'page' => 1,
                    'limit' => 30,
                ]
            );
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Fetch data
        $folders = $this->em->getRepository(Folder::class)->findForRestApi($filters);

        // Transform data
        $result = array_map(function($folder) use ($filters) {

            $folderParcels = isset($folder['minute']['plot']['parcel']) ? explode(',', $folder['minute']['plot']['parcel']) : [];

            $minute = $folder['minute'] ?? null;

            // clean parcels (remove special chars)
            $cleanFolderParcels = array_map(function($parcel) use ($minute) {
                // remove all non-alphanumeric characters
                return preg_replace('/[^A-Za-z0-9]/', '', $parcel);
            }, $folderParcels);

            // apply filters on parcels if any
            $matchedParcelsWithFilters = [];
            if (!empty($filters['plotCode'])) {
                $matchedParcelsWithFilters = array_intersect($filters['plotCode'], $cleanFolderParcels);
            }

            $departmentCode = $folder['department']['code'] ?? null;

            $closure = $folder['closure'] ?? null;
            if (!$closure) {
                $status = 'text.minute.pending';
            } else {
                $status = $closure['status'];
            }
            $link = $this->router->generate('lucca_minute_show', ['dep_code' => $departmentCode,'id' => $minute['id']], UrlGeneratorInterface::ABSOLUTE_URL);

            return [
                'departmentCode' => $departmentCode,
                'folderNumber' => $folder['num'],
                'folderLink' => $link,
                'createdAt' => $folder['createdAt'] ? $folder['createdAt']->format('Y-m-d H:i:s') : null,
                'identifiedParcels' => $matchedParcelsWithFilters,
                'allParcels' => $folderParcels,
                'status' => $this->translator->trans($status, [], 'MinuteBundle'),
            ];
        }, $folders['data']);


        return new JsonResponse([
            'data' => $result,
            ...$folders['meta']
        ], Response::HTTP_OK);
    }
}
