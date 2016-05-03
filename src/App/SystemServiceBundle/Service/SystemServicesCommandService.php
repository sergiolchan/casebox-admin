<?php

namespace App\SystemServiceBundle\Service;

/**
 * Class SystemServicesCommandService
 */
class SystemServicesCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/service.yml';

    /**
     * @param array $params
     * @return array
     */
    public function restart(array $params = [])
    {
        $commands['restart'] = self::BASE_SCRIPT.' --extra-vars="service='.$params['service'].'"';

        return $commands;
    }
}
