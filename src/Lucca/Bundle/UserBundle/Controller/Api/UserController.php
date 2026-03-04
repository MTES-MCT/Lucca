<?php

/*
 * Copyright (c) 2025-2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\UserBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\UserBundle\Entity\User;
use Lucca\Bundle\CoreBundle\Twig\BooleanExtension;

#[Route(path: '/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route(path: '/search-datatable', name: 'lucca_user_api_list_datatable', methods: ['POST'])]
    public function datatableSearchUsersAction(Request $request, BooleanExtension $booleanExtension): JsonResponse
    {
        $params = $request->request->all();
        $result = $this->entityManager->getRepository(User::class)->searchUsersForDatatable($params);

        $result['data'] = array_map(function(User $user) use ($booleanExtension) {
            $depts = [];
            foreach ($user->getAdherents() as $adherent) {
                if ($adherent->getDepartment()) {
                    $depts[] = $adherent->getDepartment()->getName();
                }
            }

            $uniqueDepts = array_unique($depts);

            return [
                'username'    => $user->getUsername(),
                'name'        => $user->getName() ?? '-',
                'email'       => $user->getEmail(),
                'lastLogin'   => $user->getLastLogin() ? $user->getLastLogin()->format('d/m/Y H:i:s') : '-',
                'enabled'     => $booleanExtension->booleanFilter($user->isEnabled()),
                'groups'      => implode('<br>', array_map(fn($g) => $g->getName(), $user->getGroups()->toArray())),
                'departments' => implode(', ', $uniqueDepts),
                'actions'     => $this->renderView('@LuccaUser/User/_actions.html.twig', [
                    'id' => $user->getId(),
                    'enabled' => $user->isEnabled()
                ])
            ];
        }, $result['data']);
//        $result['data'] = array_map(function($user) use ($booleanExtension) {
//            // Collect unique department names from all associated adherents
//            $depts = [];
//            if (!empty($user['adherents'])) {
//                foreach ($user['adherents'] as $adh) {
//                    if (isset($adh['department']['name'])) {
//                        $depts[] = $adh['department']['name'];
//                    }
//                }
//            }
//            $uniqueDepts = array_unique($depts);
//
//            return [
//                'username'    => $user['username'],
//                'name'        => $user['name'] ?? '-',
//                'email'       => $user['email'],
//                'lastLogin'   => $user['lastLogin'] ? $user['lastLogin']->format('d/m/Y H:i:s') : '-',
//                'enabled'     => $booleanExtension->booleanFilter($user['enabled']),
//                'groups'      => implode('<br>', array_column($user['groups'], 'name')),
//                'departments' => implode(', ', $uniqueDepts), // New Column!
//                'actions'     => $this->renderView('@LuccaUser/User/_actions.html.twig', [
//                    'id' => $user['id'],
//                    'enabled' => $user['enabled']
//                ])
//            ];
//        }, $result['data']);

        return new JsonResponse($result);
    }
}
