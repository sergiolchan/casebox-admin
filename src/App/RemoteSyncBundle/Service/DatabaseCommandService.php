<?php

namespace App\RemoteSyncBundle\Service;

/**
 * Class DatabaseCommandService
 */
class DatabaseCommandService
{
    /**
     * @param array $params
     *
     * @return array
     */
    public function command(array $params)
    {
        $sshUser = (!empty($params['ssh_user'])) ? $params['ssh_user'] : 'vagrant';
        $sshHost = (!empty($params['ssh_host'])) ? $params['ssh_host'] : 'localhost';
        $sshPort = (!empty($params['ssh_port'])) ? $params['ssh_port'] : 22;

        $sqlFile = $params['sql_file'];
        $parFile = $params['parameters_file'];
        $tag = $params['tag'];

        $script = 'ansible-playbook -i "'.$sshHost.'," --user='.$sshUser.' /var/provision/ansible/sync/database.yml';

        $commands['sync'] = $script.' --tags='.$tag.' --extra-vars="ansible_ssh_port='.$sshPort.' sql_file=\''.$sqlFile.'\' parameters_file=\''.$parFile.'\'"';

        return $commands;
    }
}
