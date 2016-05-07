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
    const REDIRECT_URL_SECURITY = '/admin/security';
    const REDIRECT_URL_SECURITY_SETUP = '/admin/security/setup';
    const REDIRECT_URL_SECURITY_RELOAD = '/admin/security/reload';

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

        // Reload needed
        $isReloadNeede = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->isReloadNeeded();
        $urls = [self::REDIRECT_URL_SECURITY_RELOAD];
        if (!in_array($event->getRequest()->getRequestUri(), $urls) && $isReloadNeede) {
            $event->setResponse(new RedirectResponse(self::REDIRECT_URL_SECURITY_RELOAD));
        }

        // Redirect if no ecryptfs installed
        $isEncrypted = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->isEncrypted();
        $ecryptfsReady = $this->container->get('app_dashboard.service.redis_service')->get('ecryptfs_ready');

        $urls = [self::REDIRECT_URL_SECURITY, self::REDIRECT_URL_SECURITY_SETUP];

        if (!in_array($event->getRequest()->getRequestUri(), $urls)) {
            if (empty($isEncrypted)) {
                $event->setResponse(new RedirectResponse(self::REDIRECT_URL_SECURITY));
            }
        }

        if (!empty($isEncrypted)) {
            if (!in_array($event->getRequest()->getRequestUri(), [self::REDIRECT_URL_SECURITY_SETUP]) && empty($ecryptfsReady)) {
                $event->setResponse(new RedirectResponse(self::REDIRECT_URL_SECURITY_SETUP));
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