<?php

namespace App\DashboardBundle\Traits;

/**
 * Class DateTrait
 */
trait DateTrait
{
    /**
     * @param integer|null $timestamp
     * @param string $format
     *
     * @return bool|string
     */
    public function formatDate($timestamp = null, $format = 'd.m.Y h:i:s')
    {
        if (empty($timestamp)) {
            $timestamp = time();
        }

        return date($format, $timestamp);
    }
}
