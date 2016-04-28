<?php

namespace App\DashboardBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoggingService
 */
class LoggingService
{
    /**
     * @var Container
     */
    protected $container;

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
     * @return LoggingService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
