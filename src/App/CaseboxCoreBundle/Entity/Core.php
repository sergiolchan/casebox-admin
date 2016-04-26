<?php

namespace App\CaseboxCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Core
 *
 * @ORM\Table(name="core")
 * @ORM\Entity(repositoryClass="App\CaseboxCoreBundle\Repository\CoreRepository")
 * @UniqueEntity("core_name")
 */
class Core
{
    const LOCALE_DEFAULT = 'en';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="locale", type="string", options={"default"="en"})
     */
    protected $locale = self::LOCALE_DEFAULT;

    /**
     * @var string
     * @ORM\Column(name="core_name", type="string", length=50, unique=true)
     */
    protected $coreName;

    /**
     * @var string
     * @ORM\Column(name="admin_email", type="string")
     */
    protected $adminEmail;

    /**
     * @var string
     * @ORM\Column(name="sender_email", type="string", nullable=true)
     */
    protected $senderEmail;

    /**
     * @var integer
     * @ORM\Column(name="create_at", type="integer")
     */
    protected $createAt;

    /**
     * Core constructor
     */
    public function __construct()
    {
        $this->locale = self::LOCALE_DEFAULT;
        $this->createAt = time();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

