<?php

namespace App\CaseboxCoreBundle\Repository;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use Api\MicroDbBundle\Interfaces\RepositoryInterface;
use Api\MicroDbBundle\Service\MicroDbService;
use App\CaseboxCoreBundle\Entity\Core;

/**
 * CoreRepository
 */
class CoreRepository extends MicroDbService implements RepositoryInterface
{
    /**
     * @return ObjectInterface
     */
    public function getObjectClass()
    {
        return Core::class;
    }
}
