<?php

namespace App\DashboardBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class AuthService
 */
class AuthService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return null|string
     * @throws \Exception
     */
    public function sign()
    {
        $token = $this->get();
        $this->container->get('twig')->addGlobal('app_token', $token);

        return $token;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validate()
    {
        $globals = $this->container->get('twig')->getGlobals();

        if (empty($globals['app_token'])) {
            return false;
        }

        if ($globals['app_token'] != $this->get()) {
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function get()
    {
        $token = $this->container->get('app_dashboard.service.redis_service')->get('app_token');
        if (empty($token)) {
            $token = md5(time()); // Update token generation mechanism
            $this->set($token);
        }

        return $token;
    }

    /**
     * @param string $token
     *
     * @throws \Exception
     */
    public function set($token)
    {
        // Setup cache for 5 minutes
        $this->container->get('app_dashboard.service.redis_service')->set('app_token', $token, 60 * 5);
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
     * @return LoggingService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
