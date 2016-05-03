<?php

namespace App\CaseboxCoreBundle\EventListener;

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
        $this->container->get('app_casebox_core.service.casebox_core_service')->onAppProcessResult($event);
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