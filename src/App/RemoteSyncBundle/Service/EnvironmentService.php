<?php

namespace App\RemoteSyncBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
use App\RemoteSyncBundle\Entity\Host;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class EnvironmentService
 */
class EnvironmentService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return array
     */
    public function getAllEnvironments()
    {
        $environments = [
            'Remote environment' => $this->getHostEnvironments(),
            'Local environment' => $this->getCoreEnvironments(),
        ];

        return $environments;
    }

    /**
     * @return array
     */
    public function getHostEnvironments()
    {
        $envs = [];
        $hosts = $this->getContainer()->get('app_remote_sync.repository.host_repository')->find();
        foreach ($hosts as $host) {
            if ($host instanceof Host) {
                $envs[$host->getEnvironment()] = 'host:'.$host->getEnvironment();
            }
        }

        return $envs;
    }

    /**
     * @return array
     */
    public function getCoreEnvironments()
    {
        $envs = [];

        $cores = $this->getContainer()->get('app_casebox_core.repository.core_repository')->find();
        foreach ($cores as $core) {
            if ($core instanceof Core) {
                $envs[$core->getCoreName()] = 'core:'.$core->getCoreName();
            }
        }

        return $envs;
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
     * @return EnvironmentService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
