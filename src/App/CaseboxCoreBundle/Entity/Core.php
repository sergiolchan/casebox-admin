<?php

namespace App\CaseboxCoreBundle\Entity;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use App\CaseboxCoreBundle\Repository\CoreRepository;

/**
 * Core
 */
class Core extends CoreRepository implements ObjectInterface
{
    const LOCALE_DEFAULT = 'en';

    /**
     * @var integer
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $locale = self::LOCALE_DEFAULT;

    /**
     * @var string
     */
    protected $coreName;

    /**
     * @var string
     */
    protected $adminEmail;

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var integer
     */
    protected $createAt;

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
        $this->locale = self::LOCALE_DEFAULT;
        $this->createAt = time();
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
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Core $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getCoreName()
    {
        return $this->coreName;
    }

    /**
     * @param string $coreName
     *
     * @return Core $this
     */
    public function setCoreName($coreName)
    {
        $this->coreName = $coreName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @param string $adminEmail
     *
     * @return Core $this
     */
    public function setAdminEmail($adminEmail)
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     *
     * @return Core $this
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * @param int $createAt
     *
     * @return Core $this
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }
}

