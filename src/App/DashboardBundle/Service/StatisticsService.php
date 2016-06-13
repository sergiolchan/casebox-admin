<?php

namespace App\DashboardBundle\Service;

use App\DashboardBundle\Traits\StringTrait;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class StatisticsService
 */
class StatisticsService
{
    use StringTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return array
     */
    public function cmdServerStatistics()
    {
        $file = '/tmp/dstat-rt-'.time().'.csv';

        $commands['disk_usage'] = [
            'command' => 'df -h .',
        ];

        $commands['csv_raw'] = [
            'command' => "dstat -t --cpu --mem --net --noheaders --nocolor --output {$file} 1 0",
        ];

        $commands['cat_csv_raw'] = [
            'command' => "cat {$file}",
        ];

        $commands['rm_csv_raw'] = [
            'command' => "rm {$file}",
        ];

        return $commands;
    }

    /**
     * @return string
     */
    public function getHostStatisticsHtml()
    {
        // Command
        $commands = $this->cmdServerStatistics();

        foreach ($commands as $key => $value) {
            $command = $value['command'];
            $data[$key] = shell_exec($command);
        }

        // Stat
        if (!empty($data['cat_csv_raw'])) {
            $vars = $this->getHostCpuMemeNetStatistics($data['cat_csv_raw']);
        }

        // HDD
        $diskStat = $this->getHostDiskUsage($data['disk_usage']);
        unset($diskStat['all']);

        // Disk usage.
        if (!empty($diskStat)) {
            $vars['header'] = [
                'filesystem' => [
                    'data' => 'File system',
                ],
                'size' => [
                    'data' => 'Size',
                ],
                'used' => [
                    'data' => 'Used',
                ],
                'avail' => [
                    'data' => 'Available',
                ],
                'used_percent' => [
                    'data' => 'Used in %',
                ],
            ];

            $vars['rows'][] = [
                'filesystem' => [
                    'data' => $diskStat['filesystem'],
                    'class' => '',
                ],
                'size' => [
                    'data' => $diskStat['size'],
                    'class' => '',
                ],
                'used' => [
                    'data' => $diskStat['used'],
                    'class' => '',
                ],
                'avail' => [
                    'data' => $diskStat['avail'],
                    'class' => '',
                ],
                'used_percent' => [
                    'data' => $diskStat['used_percent'],
                    'class' => '',
                ],
            ];

            $vars['disk_usage'] = $this->container->get('twig')->render(
                'AppDashboardBundle::table-bordered.html.twig',
                $vars
            );
        }

        return $this->container->get('twig')->render(
            'AppDashboardBundle::statistics-chart.html.twig',
            $vars
        );
    }

    /**
     * @param string $data
     *
     * @return array
     */
    public function getHostCpuMemeNetStatistics($data)
    {
        $vars = [
            'cpu_used' => 0,
            'cpu_free' => 0,
            'mem_used' => 0,
            'mem_free' => 0,
            'net_recv' => 0,
            'net_send' => 0,
        ];

        $dstat = $this->csvStringToArray($data);

        end($dstat);

        $dstat = $dstat[key($dstat)];

        if (!empty($dstat)) {
            $vars['cpu_used'] = round($dstat['usr'], 0, PHP_ROUND_HALF_UP);
            $vars['cpu_free'] = round($dstat['idl'], 0, PHP_ROUND_HALF_UP);
            $vars['mem_used'] = $this->humanFileSize($dstat['used']);
            $vars['mem_free'] = $this->humanFileSize($dstat['free']);
            $vars['net_recv'] = $this->humanFileSize($dstat['recv']);
            $vars['net_send'] = $this->humanFileSize($dstat['send']);
        }

        return $vars;
    }

    /**
     * @param string $data
     *
     * @return array
     */
    public function getHostDiskUsage($data)
    {
        $stat = [];

        $fields = [
            'all',
            'filesystem',
            'size',
            'used',
            'avail',
            'used_percent',
        ];

        $lines = explode(PHP_EOL, $data);

        if (!empty($lines[1])) {
            preg_match('/([^ ]*)\s+([^ ]*)\s+([^ ]*)\s+([^ ]*)\s+([^ ]*)\s+/is', $lines[1], $match);

            if (!empty($match)) {
                foreach ($fields as $key => $value) {
                    $stat[$value] = $match[$key];
                }
            }
        }

        if (empty($stat)) {
            foreach ($fields as $value) {
                $stat[$value] = '0';
            }
        }

        return $stat;
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
     * @return StatisticsService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
