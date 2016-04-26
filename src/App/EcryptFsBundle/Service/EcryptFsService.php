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

    /**
     * @param array $params
     * @return array
     */
    public function install(array $params = [])
    {
        $commands['install'] = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/ecryptfs.yml --tags=install';

        return $commands;
    }

    /**
     * @param array $params
     * @return bool|true
     */
    public function mount(array $params = [])
    {
        $commands['mount'] = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/ecryptfs.yml --tags=mount';

        return $commands;
    }

    /**
     * @param array $params
     * @return bool|true
     */
    public function umount(array $params = [])
    {
        $commands['umount'] = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/ecryptfs.yml --tags=umount';

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
