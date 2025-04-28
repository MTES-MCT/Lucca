<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Anonymous;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\CoreBundle\Form\SelectDepartmentType;

#[Route(path: '/')]
#[IsGranted('ROLE_USER')]
class PortalController extends AbstractController
{
    public function __construct(
        private readonly AdherentFinder         $adherentFinder,
        private readonly RouterInterface        $router,
    )
    {
    }

    /**
     * Select Department
     */
    #[Route(path: '/select-department', name: 'lucca_core_portal', defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function portalAction(Request $request): Response
    {
        $adherent = null;
        try {
            $adherent = $this->adherentFinder->whoAmI();
        } catch (Exception) {
        }

        $departmentsIds = $adherent?->getDepartments()
            ->map(fn ($department) => $department->getId())
            ->toArray()
        ?? [];

        $form = $this->createForm(SelectDepartmentType::class, null, [
            'ids' => $departmentsIds,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $department = $form->get('department')->getData();
            $isForAdmin = $request->request->has('adminSpace');
            if ($department && !$isForAdmin) {
                if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('lucca_core_parameter', ['domainName' => $department->getDomainName()]);
                }

                return $this->redirectToRoute('lucca_core_dashboard', ['domainName' => $department->getDomainName()]);
            }

            if ($isForAdmin && $this->getUser() && $this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('lucca_core_parameter', ['domainName' => $this->getParameter('lucca_core.admin_domain_name')]);
            }
        }

        return $this->render('@LuccaCore/select-department.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Override the render method to add the domainName in the parameters if it exists to generate the URL with it
     */
    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        /** If domainName is not in parameter, use basic method */
        if (!isset($parameters['domainName'])) {
            return parent::redirectToRoute($route, $parameters, $status);
        }

        $domainName = $parameters['domainName'];

        /** unset the domainName from the parameters */
        unset ($parameters['domainName']);

        /** Generate url from route and domainName */
        $url = 'https://' . $domainName . $this->router->generate($route, $parameters);

        /** return the redirect response with domainName */
        return $this->redirect($url);
    }
}
