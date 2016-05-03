<?php

namespace App\EcryptFsBundle\Entity;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use App\EcryptFsBundle\Repository\PassphraseRepository;

/**
 * Class Passphrase
 */
class Passphrase extends PassphraseRepository implements ObjectInterface
{
    /**
     * @var integer
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $hash;    

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray($this));
    }

    /**
     * Core constructor
     */
    public function __construct()
    {
        // code...
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Core $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return Passphrase $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }
}

