<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Controller\Anonymous;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\{RedirectResponse, RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;

#[Route(path: '/')]
#[IsGranted('ROLE_USER')]
class DispatcherController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    )
    {
    }

    /**
     * Select Department
     */
    #[Route(path: '/dispatch', name: 'lucca_core_dispatch', defaults: ['_locale' => 'fr'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function dispatchAction(Security $security): RedirectResponse|Response
    {
        $adherent = $this->em->getRepository(Adherent::class)->findOneBy(['user' =>  $this->getUser()]);
        $hasManyDepartments = $adherent?->getDepartments()->count() > 1;

        /** if adherent does not have department or has many department redirect to select department view */
        if ($hasManyDepartments || $adherent?->getDepartments()->isEmpty()) {
            return $this->redirectToRoute('lucca_core_portal');
        }

        if ($adherent?->getDepartments()->count() === 1 && !$security->isGranted('ROLE_ADMIN')) {
            $department = $adherent->getDepartments()->first();

            return $this->redirectToRoute('lucca_core_dashboard', ['subDomainKey' => $department->getCode()]);
        }

        /** else redirect to admin dashboard */
        return $this->redirectToRoute('lucca_core_portal');
    }

    /**
     * Override the render method to add the subDomainKey in the parameters if it exists to generate the URL with it
     */
    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        /** If subDomainKey is not in parameter, use basic method */
        if (!isset($parameters['subDomainKey'])) {
            return $this->redirect($this->generateUrl($route, $parameters), $status);
        }

        $subDomainKey = $parameters['subDomainKey'];

        /** unset the subDomainKey from the parameters */
        unset ($parameters['subDomainKey']);

        /** set or reset the subDomainKey in the session */
        $this->requestStack->getSession()->set('subDomainKey', $subDomainKey);

        $url = $this->getParameter('lucca_core.url');

        /** Generate url from route and subDomainKey */
        $url = str_replace('SUBDOMAINKEY', $subDomainKey, $url) . $this->router->generate($route, $parameters);

        /** url cleaner */
        $url = str_replace('https://.', 'https://', $url);
        $url = str_replace('https://-', 'https://', $url);
        $url = str_replace('..', '.', $url);
        $url = str_replace('-.', '.', $url);

        /** return the redirect response with subDomainKey */
        return new RedirectResponse($url, $status);
    }
}
