<?php

namespace App\DashboardBundle\Service;

use Predis\Client;

/**
 * Class RedisService
 */
class RedisService
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param string $key Cache key
     * @param string $value Cache value
     * @param int|null $ttl Cache timeout in seconds
     *
     * @see http://redis.io/commands/set
     *
     * @return Client
     */
    public function set($key, $value, $ttl = null)
    {
        if (!empty($ttl) && $ttl > 0) {
            return $this->getRedisClient()->set($key, $value, 'EX', $ttl);
        } else {
            return $this->getRedisClient()->set($key, $value);
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        return $this->getRedisClient()->get($key);
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function finKey($key)
    {
        $result = $this->getRedisClient()->scan(0, ['MATCH' => $key]);

        return (!empty($result[1])) ? $result[1] : null;
    }

    /**
     * @param array $keys
     *
     * @return int
     */
    public function del(array $keys)
    {
        return $this->getRedisClient()->del($keys);
    }

    /**
     * @return Client
     */
    public function flushAll()
    {
        return $this->getRedisClient()->flushall();
    }

    /**
     * @return Client
     */
    public function getRedisClient()
    {
        return new Client($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return RedisService $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}
