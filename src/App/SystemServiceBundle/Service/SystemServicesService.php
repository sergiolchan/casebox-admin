<?php

namespace App\SystemServiceBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class SystemServicesService
 */
class SystemServicesService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @todo - Defile service list available for extend.
     * @var array
     */
    public static $services = [
        'nginx' => 'nginx',
        'solr' => 'solr',
    ];

    /**
     * @param string $service
     * @param array $data
     *
     * @return bool
     */
    public function restartServices($service, array  $data = [])
    {
        if (!empty(self::$services[$service])) {
            return $this->restart($service);
        }
        
        return false;
    }

    /**
     * @param string $service
     *
     * @throws \Exception
     */
    public function restart($service)
    {
        $data['app_system_service.service.system_services_command_service']['restart'] = ['service' => $service];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);
        
        return true;
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
     * @return SystemServicesService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
