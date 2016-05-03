<?php

namespace App\EcryptFsBundle\EventListener;

use App\DashboardBundle\Event\ProcessResultEvent;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AppProcessResultListener
 */
class AppProcessResultListener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param ProcessResultEvent $event
     */
    public function onOnAppProcessResult(ProcessResultEvent $event)
    {
        $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->onAppProcessResult($event);
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
     * @return AppProcessResultListener $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}