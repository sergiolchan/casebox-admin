<?php

namespace App\DashboardBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestListener
 */
class RequestListener
{
    const REDIRECT_URL = '/admin/install';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param GetResponseEvent $event
     * @return GetResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        // Sign app
        $this->container->get('app_dashboard.service.auth_service')->sign();

        // Redirect if no ecryptfs installed
        $isEcryptfsPass = $this->container->get('app_dashboard.service.redis_service')->get('is_ecryptfs');

        $urls = [self::REDIRECT_URL];

        if (!in_array($event->getRequest()->getRequestUri(), $urls)) {
            if (empty($isEcryptfsPass)) {
                return $event->setResponse(new RedirectResponse(self::REDIRECT_URL));
            }
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