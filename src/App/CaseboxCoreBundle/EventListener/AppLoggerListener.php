<?php

namespace App\CaseboxCoreBundle\EventListener;

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
        // code...
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