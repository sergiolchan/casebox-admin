<?php

namespace App\SecurityBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use App\DashboardBundle\EventListener\RequestListener as DashboardRequestListener;

/**
 * Class RequestListener
 */
class RequestListener
{
    const REDIRECT_URL_SECURITY_APP = '/admin/security/app';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param GetResponseEvent $event
     *
     * @return GetResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $urls = [
            self::REDIRECT_URL_SECURITY_APP,
            DashboardRequestListener::REDIRECT_URL_SECURITY,
            DashboardRequestListener::REDIRECT_URL_SECURITY_SETUP,
            DashboardRequestListener::REDIRECT_URL_SECURITY_RELOAD,
        ];

        $isSecured = $this->container->get('app_security.service.security_service')->isSecured();
        if (!in_array($event->getRequest()->getRequestUri(), $urls) && empty($isSecured)) {
            $event->setResponse(new RedirectResponse(self::REDIRECT_URL_SECURITY_APP));
        }
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     *
     * @return RequestListener $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}