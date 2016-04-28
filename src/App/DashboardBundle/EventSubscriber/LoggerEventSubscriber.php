<?php

namespace App\DashboardBundle\EventSubscriber;

use App\DashboardBundle\Event\LoggerEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoggerEventSubscriber
 */
class LoggerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param LoggerEvent $event
     */
    public function onAppLogger(LoggerEvent $event)
    {
        // code...
    }

    /**
     * @return array
     */
    static function getSubscribedEvents()
    {
        return [
            'on.app.logger' => 'onAppLogger',
        ];
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
     * @return LogEventSubscriber $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
