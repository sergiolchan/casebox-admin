<?php

namespace App\SystemLogBundle\EventListener;

use App\DashboardBundle\Event\LoggerEvent;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AppLoggerListener
 */
class AppLoggerListener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param LoggerEvent $event
     */
    public function onOnAppLogger(LoggerEvent $event)
    {
        $this->container->get('app_system_log.service.system_log_service')->onOnAppLogger($event);
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
     * @return AppLoggerListener $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}