<?php

namespace App\DashboardBundle\EventSubscriber;

use App\DashboardBundle\Event\QueueEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class QueueEventSubscriber
 */
class QueueEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param QueueEvent $event
     */
    public function onAppQueueWrite(QueueEvent $event)
    {
        // code...
    }

    /**
     * @param QueueEvent $event
     */
    public function onAppQueueRead(QueueEvent $event)
    {
        // code...
    }


    /**
     * @param QueueEvent $event
     */
    public function onAppQueueClose(QueueEvent $event)
    {
        // code...
    }

    /**
     * @return array
     */
    static function getSubscribedEvents()
    {
        return [
            'on.app.queue.write' => 'onAppQueueWrite',
            'on.app.queue.read'  => 'onAppQueueRead',
            'on.app.queue.close' => 'onAppQueueClose',
        ];
    }
}
