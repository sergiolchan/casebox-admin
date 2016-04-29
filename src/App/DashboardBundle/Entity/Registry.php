<?php

namespace App\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registry
 *
 * @ORM\Table(name="registry")
 * @ORM\Entity(repositoryClass="App\DashboardBundle\Repository\RegistryRepository")
 */
class Registry
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    protected $data;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Registry $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|array|integer
     */
    public function getData()
    {
        return unserialize($this->data);
    }

    /**
     * @param string|array|integer $data
     *
     * @return Registry $this
     */
    public function setData($data)
    {
        $this->data = serialize($data);

        return $this;
    }
}

