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

        //get query params
        $isAdminScope = filter_var(
            $request->query->get('isAdminScope', false),
            FILTER_VALIDATE_BOOLEAN
        );

        // call repository method
        $result = $this->entityManager->getRepository(Adherent::class)
            ->searchAdherentsForDatatable($params, $isAdminScope);

        // format final result
        $result['data'] = array_map(function(Adherent $adherent) use ($isAdminScope) {

            $value = [
                'officialName' => $adherent->getOfficialName(),
                'email' => $adherent->getUser()->getEmail(),
                'phone' => $adherent->getPhone() ?? '-',
                'location' => $adherent->getService()?->getName() ?? $adherent->getIntercommunal()?->getName() ?? $adherent->getTown()?->getName() ?? '-',
                'address' => $adherent->getOfficialAddressInline(),
                'username' => $adherent->getUser()->getUsername(),
                'groups' => implode('<br>', array_map(fn($g) => $g->getName(), $adherent->getUser()->getGroups()->toArray())),
                'logo' => $this->booleanExtension->booleanFilter($adherent->getLogo()),
                'actions' => $this->renderView('@LuccaAdherent/Adherent/_actions.html.twig', [
                    'id' => $adherent->getId(),
                    'enabled' => $adherent->isEnabled()
                ])
            ];

            if ($isAdminScope) {
                $value['department'] = $adherent->getDepartment()->formLabel();
            }

            return $value;

        }, $result['data']);

        return new JsonResponse($result);
    }

}
