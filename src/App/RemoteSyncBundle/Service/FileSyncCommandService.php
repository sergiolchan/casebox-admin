<?php

namespace App\RemoteSyncBundle\Service;

/**
 * Class FileSyncCommandService
 */
class FileSyncCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/sync/files.yml';

    /**
     * @param array $params
     *
     * @return array
     */
    public function command(array $params)
    {
        $user = $params['ssh_user'];
        $host = $params['ssh_host'];
        $port = $params['ssh_port'];

        $terminator = '';
        if (!empty($params['terminator'])) {
            $terminator = '/';
        }

        $source = $params['source'].$terminator;
        $destination = $params['destination'].$terminator;

        $tag = $params['tag'];

        $commands['sync'] = self::BASE_SCRIPT.' --tags='.$tag.' --extra-vars="ssh_user='.$user.' ssh_host='.$host.' ssh_port='.$port.' source='.$source.' destination='.$destination.'"';

        return $commands;
    }
}
