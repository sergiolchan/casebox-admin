<?php

namespace App\RemoteSyncBundle\Service;

use App\DashboardBundle\Service\MessageService;
use App\RemoteSyncBundle\Entity\Host;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class HostService
 */
class HostService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $address
     *
     * @return null|Host
     */
    public function getHostByAddress($address)
    {
        $host = $this->container->get('app_remote_sync.repository.host_repository')->findOne(
            ['address' => $address]
        );

        return $host;
    }

    /**
     * @param string $env
     *
     * @return null|Host
     */
    public function getHostByEnvironment($env)
    {
        $host = $this->container->get('app_remote_sync.repository.host_repository')->findOne(
            ['environment' => $env]
        );

        return $host;
    }

    /**
     * @param array $data
     *
     * @return Host|null
     * @throws \Exception
     */
    public function addHost(array $data)
    {
        $host = $this->getHostByAddress($data['address']);
        if ($host instanceof Host) {
            $this->container->get('session')->getFlashBag()->add(
                'warning',
                sprintf(MessageService::HOST_EXIST, $data['address'])
            );

            return $host;
        }

        $host = new Host();
        $host->setAddress($data['address']);
        $host->setDocroot($data['docroot']);
        $host->setEnvironment($data['environment']);
        $host->setDescription($data['description']);
        $this->container->get('app_remote_sync.repository.host_repository')->save($host);

        return $host;
    }

    /**
     * @param Host $host
     *
     * @return Host
     */
    public function editHost(Host $host)
    {
        $this->container->get('app_remote_sync.repository.host_repository')->save($host);

        return $host;
    }

    /**
     * @param Host $host
     *
     * @return bool|true
     */
    public function deleteHost(Host $host)
    {
        // Delete
        $this->container->get('app_remote_sync.repository.host_repository')->delete($host);

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
     * @return HostService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
