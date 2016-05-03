<?php

namespace App\EcryptFsBundle\Repository;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use Api\MicroDbBundle\Interfaces\RepositoryInterface;
use Api\MicroDbBundle\Service\MicroDbService;
use App\EcryptFsBundle\Entity\Passphrase;

/**
 * PassphraseRepository
 */
class PassphraseRepository extends MicroDbService implements RepositoryInterface
{
    /**
     * @return ObjectInterface
     */
    public function getObjectClass()
    {
        return Passphrase::class;
    }

    /**
     * @return string $alias
     */
    public function getDbAlias()
    {
        return 'passphrase';
    }
}
