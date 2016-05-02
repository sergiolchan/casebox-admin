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
            foreach ($config['routes'] as $vars) {
                $route = $vars['route'];
                $class = new $vars['class']();
                $params = $vars['params'];

                $app->route($route, $class, $params);
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
