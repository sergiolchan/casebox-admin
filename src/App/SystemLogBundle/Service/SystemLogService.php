<?php

namespace App\SystemLogBundle\Service;

use App\DashboardBundle\Event\LoggerEvent;
use App\DashboardBundle\Service\MessageService;
use App\DashboardBundle\Traits\DateTrait;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SystemLogService
 */
class SystemLogService
{
    use DateTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return array
     */
    public function getLogs()
    {
        $result['logs'] = '';

        $logsArray = $this->getLogsFromCache();
        foreach ($logsArray as $item) {
            if (empty($item['message'])) {
                continue;
            }

            $result['logs'] .= $this->getLogHtml($item);
        }

        if (empty($result['logs'])) {
            $result['logs'] = '<p style="color:white;" ">'.MessageService::LOGS_NOT_FOUND.'</p>';
        }
        
        return $result;
    }

    /**
     * @param LoggerEvent $event
     *
     * @return array
     * @throws \Exception
     */
    public function onOnAppLogger(LoggerEvent $event)
    {
        $logsArray = $this->getLogsFromCache();

        $element = [
            'type' => $event->getType(),
            'timestamp' => (!empty($event->getTimestamp())) ? $event->getTimestamp() : time(),
            'message' => $event->getMessage(),
            'context' => (!empty($event->getContext())) ? $event->getContext() : '',
        ];

        array_unshift($logsArray, $element);

        $this->container->get('app_dashboard.service.redis_service')->set('queue_logs', json_encode($logsArray), 60 * 60 * 5);

        return $logsArray;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getLogsFromCache()
    {
        $logsArray = [];
        $logs = $this->container->get('app_dashboard.service.redis_service')->get('queue_logs');
        if (!empty($logs)) {
            $logsArray = json_decode($logs, true);
        }

        return $logsArray;
    }

    /**
     * @param array $item
     *
     * @return string
     * @throws \Exception
     */
    public function getLogHtml(array $item)
    {
        $type = 'default';
        
        if (!empty($item['type'])) {
            $type = $item['type'];
        }

        $message = '';

        if (!empty($item['message'])) {
            $message = $item['message'];
        }

        $vars = [
            'type' => $type,
            'message' => $message,
        ];

        $vars['context'] = '';
        if (!empty($item['context'])) {
            $vars['context'] = implode(" * ", $item['context']);
        }

        $vars['timestamp'] = $this->formatDate(time());
        if (!empty($item['timestamp'])) {
            $vars['timestamp'] = $this->formatDate($item['timestamp']);
        }

        $html = $this->container->get('twig')->render('AppSystemLogBundle::log-item.html.twig', $vars);

        return $html;
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
     * @return SystemLogService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
