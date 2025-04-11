<?php

namespace Lucca\Bundle\CoreBundle\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

readonly class SubdomainChangeListener implements EventSubscriberInterface
{

    public function __construct(
        private RequestStack          $requestStack,
        private RouterInterface       $router,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        $subDomainUrlKey = $this->extractSubdomainKey($request->getHost());
        $session = $this->requestStack->getSession();
        $env =  $this->parameterBag->get('kernel.environment');
        $previousSubdomainKey = $session->get('subDomainKey');

        $routeWithoutSubdomainRoutes = [
//            'lucca_user_security_login',
//            'lucca_user_security_forget_password',
//            'lucca_user_security_change_password',
//            'lucca_user_security_activate_user',
//            'lucca_user_security_logout',
//            'lucca_core_portal'
        ];

        if ($subDomainUrlKey && in_array($routeName, $routeWithoutSubdomainRoutes)) {

            $session->remove('subDomainKey');

            /** set or reset the subDomainKey in the session */
            $url =  $this->parameterBag->get('lucca_core.url');
            /** Generate url from route and subDomainKey (use home for dev environment because without dose not working) */
            $url = str_replace('SUBDOMAINKEY', $env === 'dev' ? 'home' : '', $url).$this->router->generate($routeName);

            /** url cleaner */
            $url = str_replace('https://.', 'https://', $url);
            $url = str_replace('https://-', 'https://', $url);
            $url = str_replace('..', '.', $url);
            $url = str_replace('-.', '.', $url);

            /** return the redirect response without subDomainKey */
            $event->setResponse(new RedirectResponse($url));

            return;
        }

        if ($previousSubdomainKey !== $subDomainUrlKey) {
            $session->set('subDomainKey', $subDomainUrlKey);
        }
    }
    private function extractSubdomainKey($host): ?string
    {
        /** Regex all department number in france */
        if (preg_match('/(0[1-9]|[1-8][0-9]|9[0-5]|2[AB]|97[1-6])/', $host, $matches)) {
            return $matches[0] ?? null;
        }

        return null;
    }
}
