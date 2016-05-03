<?php

namespace App\EcryptFsBundle\Service;

use App\DashboardBundle\Event\ProcessResultEvent;
use App\EcryptFsBundle\Entity\Passphrase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class EcryptFsService
 */
class EcryptFsService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $coreName
     * @return Passphrase
     * @throws \Exception
     */
    public function getPassphrase()
    {
        // $this->container->get('app_dashboard.service.redis_service')->get('is_ecryptfs');
        $passphrase = $this->container->get('app_ecrypt_fs.repository.passphrase_repository')->findOne([]);

        return $passphrase;
    }

    /**
     * @return string
     */
    public function isEncrypted()
    {
        $output = shell_exec("grep '/var/www/.lock ecryptfs' /etc/mtab");

        $this->container->get('app_dashboard.service.redis_service')->set('ecryptfs_ready', 1);

        return $output;
    }

    /**
     * @param string $passphrase
     *
     * @return bool
     * @throws \Exception
     */
    public function passphrase($passphrase)
    {
        if (!$this->getPassphrase() instanceof Passphrase) {
            $object = new Passphrase();
            $object->setHash(sha1($passphrase));
            $this->container->get('app_ecrypt_fs.repository.passphrase_repository')->save($object);
        }

        // Validate passphrase
        if ($this->getPassphrase()->getHash() != sha1($passphrase)) {
            return false;
        }

        // Try to mount partitions
        // Stop services
        //$data['app_ecrypt_fs.service.ecrypt_fs_command_service']['stopServices'] = ['passphrase' => $passphrase];
        //$this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        // Install ecryptfs
        $data['app_ecrypt_fs.service.ecrypt_fs_command_service']['install'] = ['passphrase' => $passphrase];
        // Mount ecryptfs
        $data['app_ecrypt_fs.service.ecrypt_fs_command_service']['mount'] = ['passphrase' => $passphrase];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        // Set to registry and redis
        $this->container->get('app_dashboard.service.redis_service')->set('is_ecryptfs', 1);

        return true;
    }

    /**
     * Update object status.
     *
     * @param ProcessResultEvent $event
     */
    public function onAppProcessResult(ProcessResultEvent $event)
    {
        $params = $event->getParams();

        if (!empty($params['app_ecrypt_fs.service.ecrypt_fs_command_service'])) {
            foreach ($params['app_ecrypt_fs.service.ecrypt_fs_command_service'] as $method => $values) {
                if ($method == 'mount') {
                    if (!empty($values['process']['out'])) {
                        $message = '';
                        foreach ($values['process']['out'] as $key => $value) {
                            if (strstr($value, 'FAILED!')) {
                                $raw = str_replace('fatal: [localhost]: FAILED! =>', '', $value);
                                $messageArray = json_decode($raw, true);
                                if (!empty($messageArray['stdout_lines'])) {
                                    $message = implode("\n", $messageArray['stdout_lines']);
                                }
                            }
                        }
                        if (!empty($message)) {
                            $this->container->get('session')->getFlashBag()->add('warning', $message);
                        } else {
                            $this->container->get('app_dashboard.service.redis_service')->set('ecryptfs_ready', 1);
                        }
                    }
                }
            }
        }
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
     * @return EcryptFsService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
