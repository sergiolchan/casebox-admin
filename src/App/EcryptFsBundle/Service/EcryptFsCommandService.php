<?php

namespace App\EcryptFsBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class EcryptFsCommandService
 */
class EcryptFsCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/ecryptfs.yml';
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param array $params
     * @return array
     */
    public function stopServices(array $params = [])
    {
        $commands['nginx'] = 'service nginx stop';
        $commands['mysql'] = 'service mysql stop';
        $commands['solr'] = 'service solr stop';

        return $commands;
    }

    /**
     * @param array $params
     * @return array
     */
    public function install(array $params = [])
    {
        $commands['install'] = self::BASE_SCRIPT.' --tags=install';

        return $commands;
    }

    /**
     * @param array $params
     * @return bool|true
     */
    public function mount(array $params = [])
    {
        $folders = [
            '/var/www/casebox',
            '/var/www/.lock',
            '/var/solr/data',
            '/var/lib/mysql',
        ];

        $commands = [];

        foreach ($folders as $key => $folder) {
            $commands['mount_'.$key] = self::BASE_SCRIPT.' --tags=mount --extra-vars="passphrase='.$params['passphrase'].' encrypted_root=\''.$folder.'\'"';
        }

        return $commands;
    }

    /**
     * @param array $params
     * @return bool|true
     */
    public function umount(array $params = [])
    {
        $commands['umount'] = self::BASE_SCRIPT.' --tags=umount';

        return $commands;
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
