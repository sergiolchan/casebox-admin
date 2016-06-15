<?php

namespace App\SecurityBundle\Service;

use App\SecurityBundle\Entity\MasterPassword;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class SecurityService
 */
class SecurityService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function isSecured()
    {
        $u = $this->container->get('session')->get('_user');
        if (empty($u)) {
            return false;
        }

        return unserialize($u);
    }

    /**
     * @return SecurityService
     * @throws \Exception
     */
    public function getMasterPassword()
    {
        $password = $this->container->get('app_security.repository.master_password_repository')->findOne([]);

        return $password;
    }

    /**
     * @param string $password
     *
     * @return bool
     * @throws \Exception
     */
    public function setMasterPassword($password)
    {
        $user = $this->getMasterPassword();
        if (!$user instanceof MasterPassword) {
            $user = new MasterPassword();
            $user->setUsername('root');
            $user->setPassword(sha1($password));

            $this->container->get('app_security.repository.master_password_repository')->save($user);
        }

        // Validate master password
        if ($user->getPassword() != sha1($password)) {
            return false;
        }

        $this->container->get('session')->start();
        $this->container->get('session')->set('_user', serialize($user));
        $this->container->get('session')->save();

        return $user;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     *
     * @return SecurityService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
