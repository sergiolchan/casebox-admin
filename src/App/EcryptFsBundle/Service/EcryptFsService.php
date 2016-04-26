<?php

namespace App\EcryptFsBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class EcryptFsService
 */
class EcryptFsService
{
    /**
     * @var Container
     */
    protected $container;

    public function passphrase($passphrase)
    {
        // Try to mount partitions
        // Stop services
        //$data['app_ecrypt_fs.service.ecrypt_fs_command_service']['stopServices'] = ['passphrase' => $passphrase];
        //$this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        // Install ecryptfs
        $data['app_ecrypt_fs.service.ecrypt_fs_command_service']['install'] = ['passphrase' => $passphrase];
        // Mount ecryptfs
        $data['app_ecrypt_fs.service.ecrypt_fs_command_service']['mount'] = ['passphrase' => $passphrase];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        // Set to registry and redis
        $this->container->get('app_dashboard.service.registry_service')->set('is_ecryptfs', 1);
        $this->container->get('app_dashboard.service.redis_service')->set('is_ecryptfs', 1);

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
     * @return EcryptFsService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
