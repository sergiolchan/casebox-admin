<?php

namespace App\DashboardBundle\Service;

use App\DashboardBundle\Event\LoggerEvent;
use App\DashboardBundle\Event\ProcessResultEvent;
use App\DashboardBundle\Traits\StringTrait;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\Process;

/**
 * Class CommandService
 */
class CommandService
{
    use StringTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected static $results = [];

    /**
     * @var bool|true
     */
    protected $log = true;

    /**
     * @param array $params
     */
    public function execute(array $params)
    {
        $result = [];

        foreach ($params as $key => $values) {
            if ($this->container->has($key)) {
                $service = $this->container->get($key);
                foreach ($values as $method => $value) {
                    try {
                        $commands = $service->$method($value);
                    } catch (\Exception $e) {
                        $this->logger->addError(sprintf(" [!] ERROR! %s", $e->getMessage()));
                    }
                    foreach ($commands as $alias => $command) {
                        $result[$key][$method]['process'] = $this->process($alias, $command);
                    }
                    $result[$key][$method]['params'] = $value;
                }
            }
        }

        // Dispatch result
        $this
            ->container
            ->get('event_dispatcher')
            ->dispatch('on.app.process_result', new ProcessResultEvent($result))
        ;
    }

    /**
     * @param string $alias Command alias ex. create|start|destroy etc.
     * @param string $command Command to execute
     * @param bool|true $log Log command execution
     *
     * @return array
     */
    public function process($alias, $command, $log = true)
    {
        self::$results = [];

        $message = " [*] Run '$alias' command ";
        $this->getLogger()->addInfo($message, [$this->filterOutput($command)]);
        $this->container->get('event_dispatcher')->dispatch(
            'on.app.logger',
            new LoggerEvent($this->logger, 'success', $message, [$this->filterOutput($command)])
        );
        $this->log = $log;

        $process = new Process($command, null, null, null, null);
        $process->run(
            function ($type, $buffer) {
                $lines = explode(chr(10), $buffer);

                foreach ($lines as $line) {
                    $state = 'success';
                    if (!empty($line)) {
                        if (Process::ERR === $type) {
                            $state = 'warning';

                            if ($this->log)
                                $this->logger->addError($line, []);
                        } else {
                            if ($this->log)
                                $this->logger->addInfo($line, []);
                        }

                        if ($this->log) {
                            $this
                                ->container
                                ->get('event_dispatcher')
                                ->dispatch('on.app.logger', new LoggerEvent($this->logger, $state, $line, []))
                            ;
                        }
                    }

                    self::$results[$type][] = $line;
                }

            }
        );

        return self::$results;
    }

    /**
     * @return Logger
     * @throws \Exception
     */
    public function getLogger()
    {
        return $this->logger = $this->container->get('app_dashboard.service.queue_logger');
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
     * @return CommandService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
