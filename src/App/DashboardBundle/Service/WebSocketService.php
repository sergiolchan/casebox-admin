<?php

namespace App\DashboardBundle\Service;

use Ratchet\App;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WebSocketService
 */
class WebSocketService
{
    /**
     * @var Container
     */
    protected $container;

    // code..

    /**
     * Run websocket server
     */
    public function server()
    {
        $config = $this->container->getParameter('ratchet');

        $app = new App($config['host'], $config['port']);

        if (!empty($config['routes'])) {
<<<<<<< HEAD
            foreach ($config['routes'] as $vars) {
                $route = $vars['route'];
                $class = $vars['class'];
                $params = $vars['params'];

                $app->route($route, new $class, $params);
=======
            foreach ($config['routes'] as $route) {
                $route = $route['route'];
                $class = $route['class'];
                $params = $route['params'];

                $app->route($route, $class, $params);
>>>>>>> 8b4d6ff7411e2df2beaade0311308495ffadae8f
            }
        }

        $app->run();
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
     * @return WebSocketService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
