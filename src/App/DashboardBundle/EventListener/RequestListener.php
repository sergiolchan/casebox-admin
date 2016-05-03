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
        $isEncrypted = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->isEncrypted();

        $urls = [self::REDIRECT_URL];

        if (!in_array($event->getRequest()->getRequestUri(), $urls)) {
            if (empty($isEncrypted)) {
                $event->setResponse(new RedirectResponse(self::REDIRECT_URL));
            } else {
                //$this->container->get('app_dashboard.service.redis_service')->set('ecryptfs_ready', 1);
            }
        }

        if (!empty($isEncrypted)) {
            $ecryptfsReady = $this->container->get('app_dashboard.service.redis_service')->get('ecryptfs_ready');
            if (!in_array($event->getRequest()->getRequestUri(), ['/']) && empty($ecryptfsReady)) {
                $event->setResponse(new RedirectResponse('/'));
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