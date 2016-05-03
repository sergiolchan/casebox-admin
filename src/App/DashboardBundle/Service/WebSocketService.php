<?php

namespace App\DashboardBundle\Service;

use App\DashboardBundle\Service\WebSocketComponent\LoggingsWampPubSub;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
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

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        new LoggingsWampPubSub()
                    )
                )
            ),
            $config['port'],
            $config['host']
        );
        
        $server->run();
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
