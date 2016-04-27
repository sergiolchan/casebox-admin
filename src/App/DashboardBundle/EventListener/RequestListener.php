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
    const INSTALL_REDIRECT_URL = '/admin/install';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // Redirect if no ecryptfs installed
        $isEcryptfsInstalled = $this->container->get('app_dashboard.service.registry_service')->get(
            'is_ecryptfs',
            false
        );

        $isEcryptfsPass = $this->container->get('app_dashboard.service.redis_service')->get('is_ecryptfs');

        $urls = [self::INSTALL_REDIRECT_URL];
        if (!in_array($event->getRequest()->getRequestUri(), $urls)) {
            if (empty($isEcryptfsInstalled) || empty($isEcryptfsPass)) {
                return new RedirectResponse(self::INSTALL_REDIRECT_URL);
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