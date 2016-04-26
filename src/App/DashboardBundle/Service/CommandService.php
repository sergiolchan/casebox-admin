<?php

namespace App\DashboardBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class CommandService
 */
class CommandService
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
     * @return ExecuteCommandService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
