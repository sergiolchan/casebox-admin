<?php

namespace App\DashboardBundle\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class QueueService
 */
class QueueService
{
    const APP_QUEUE_COMMAND = 'app/command';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool|false
     */
    protected $log = false;

    /**
     * @return AMQPStreamConnection
     */
    public function connection()
    {
        $config = $this->getRabbitmqConfig();

        return new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
    }

    /**
     * @param array $data
     * @param string $queueChannel
     *
     * @return bool
     */
    public function queueWrite(array $data, $queueChannel = self::APP_QUEUE_COMMAND)
    {
        $connection = $this->connection();
        $channel = $connection->channel();
        $channel->queue_declare($queueChannel, false, true, false, false);
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', $queueChannel);
        $channel->close();

        return $connection->close();
    }

    /**
     * @param string $queueChannel
     * @param bool|false $log
     *
     * @return bool
     */
    public function queueRead($queueChannel = self::APP_QUEUE_COMMAND, $log = false)
    {
        $connection = $this->connection();
        $channel = $connection->channel();
        $channel->queue_declare($queueChannel, false, true, false, false);

        $this->logger = $this->getQueueLogger();
        $this->logger->addInfo(" [*] Waiting for messages. To exit press CTRL+C", []);
        $this->log = $log;

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume(
            $queueChannel,
            '',
            false,
            false,
            false,
            false,
            function ($msg) {
                if ($this->log) {
                    $this->logger->addInfo(" [x] Received", ['body' => $msg->body]);
                }

                $body = json_decode($msg->body, true);
                if (is_array($body)) {
                    // Execute
                    $this->execute($body);

                    $this->logger->addInfo(" [x] DONE!");
                }

                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();

        return $connection->close();
    }

    /**
     * @param array $params
     */
    public function execute(array $params)
    {
        foreach ($params as $key => $values) {
            switch ($key) {

            }
        }
    }

    /**
     * @return array
     */
    public function getRabbitmqConfig()
    {
        return $this->container->getParameter('rabbitmq');
    }

    /**
     * @return Logger
     * @throws \Exception
     */
    public function getQueueLogger()
    {
        return $this->container->get('app_dashboard.service.queue_logger');
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
