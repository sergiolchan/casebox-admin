<?php

namespace App\RemoteSyncBundle\Repository;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use Api\MicroDbBundle\Interfaces\RepositoryInterface;
use Api\MicroDbBundle\Service\MicroDbService;
use App\RemoteSyncBundle\Entity\Host;

/**
 * HostRepository
 */
class HostRepository extends MicroDbService implements RepositoryInterface
{
    /**
     * @return ObjectInterface
     */
    public function getObjectClass()
    {
        return Host::class;
    }

    /**
     * @return string $alias
     */
    public function getDbAlias()
    {
        return 'host';
    }
}
