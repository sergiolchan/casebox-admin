<?php

namespace App\SecurityBundle\Entity;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use App\SecurityBundle\Repository\MasterPasswordRepository;

/**
 * Class MasterPassword
 */
class MasterPassword extends MasterPasswordRepository implements ObjectInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

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
     * @return MasterPassword $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return MasterPassword $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return MasterPassword $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}

