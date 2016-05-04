<?php

namespace App\ShareBundle\Service;

/**
 * Class ShareCommandService
 */
class ShareCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/sharing/casebox.yml';

    /**
     * @param array $params
     *
     * @return array
     */
    public function install(array $params = [])
    {
        $commands['install'] = self::BASE_SCRIPT;

        return $commands;
    }
}
