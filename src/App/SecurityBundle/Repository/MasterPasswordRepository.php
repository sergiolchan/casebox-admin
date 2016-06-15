<?php

namespace App\SecurityBundle\Repository;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use Api\MicroDbBundle\Interfaces\RepositoryInterface;
use Api\MicroDbBundle\Service\MicroDbService;
use App\SecurityBundle\Entity\MasterPassword;

/**
 * MasterPasswordRepository
 */
class MasterPasswordRepository extends MicroDbService implements RepositoryInterface
{
    /**
     * @return ObjectInterface
     */
    public function getObjectClass()
    {
        return MasterPassword::class;
    }

    /**
     * @return string $alias
     */
    public function getDbAlias()
    {
        return 'masterpassword';
    }
}
