<?php

namespace App\CaseboxCoreBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class CaseboxCoreCommandService
 */
class CaseboxCoreCommandService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param array|string|null $params
     *
     * @return mixed
     */
    public function create($params = null)
    {
        $command['ping'] = 'ping -c 3 8.8.8.8';
        
        return $command;
    }
    
    /**
     * @param array|string|null $params
     *
     * @return mixed
     */
    public function remove($params = null)
    {
        $command['ping'] = 'ping -c 3 8.8.8.8';
        
        return $command;
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
     * @return CaseboxCoreCommandService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
