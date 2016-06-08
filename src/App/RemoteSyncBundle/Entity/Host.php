<?php

namespace App\RemoteSyncBundle\Entity;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use App\RemoteSyncBundle\Repository\HostRepository;

/**
 * Host
 */
class Host extends HostRepository implements ObjectInterface
{
    const PORT_DEFAULT    = 22;
    const DOCROOT_DEFAULT = '/var/www/casebox';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $docroot;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $description;
    
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
        $this->port = self::PORT_DEFAULT;
        $this->docroot = self::DOCROOT_DEFAULT;
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
     * @return Host $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Host $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @param int $port
     *
     * @return Host $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocroot()
    {
        return $this->docroot;
    }

    /**
     * @param string $docroot
     *
     * @return Host $this
     */
    public function setDocroot($docroot)
    {
        $this->docroot = $docroot;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     *
     * @return Host $this
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Host $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
