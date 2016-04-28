<?php

namespace App\DashboardBundle\Traits;

use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Parser;

/**
 * Class StringTrait
 */
trait StringTrait
{
    /**
     * @param string $string
     *
     * @return string
     */
    protected function escapeshellcmd($string)
    {
        $string = str_replace('"', '\\"', $string);

        return $string;
    }

    /**
     * @param string $string
     * @param string $group
     *
     * @return string
     */
    public function filterOutput($string, $group = 'logging')
    {
        $patterns = [];

        $replacement = '********';
        switch ($group) {
            case 'logging':
                $patterns = [
                    [
                        'pattern' => '/--password \$\(echo([^|]*)\| openssl passwd -1 -stdin\)/i',
                        'replacement' => '--password '.$replacement,
                    ],
                    [
                        'pattern' => '/passphrase=([^"]*)/i',
                        'replacement' => 'passphrase='.$replacement,
                    ],
                    [
                        'pattern' => '/passphrase_passwd=([^,]*)/i',
                        'replacement' => 'passphrase_passwd='.$replacement.',',
                    ],
                ];

                break;
        }

        if (empty($patterns)) {
            return $string;
        }

        foreach ($patterns as $pattern) {
            $string = preg_replace($pattern['pattern'], $pattern['replacement'], $string);
        }

        return $string;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateAlpha($length = 13)
    {
        return $this->generateRandomString($length, $alpha = true, $num = false);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateNumeric($length = 13)
    {
        return $this->generateRandomString($length, $alpha = false, $num = true);
    }

    /**
     * @param int $length
     * @param bool $alpha
     * @param bool $num
     *
     * @return string
     */
    public function generateRandomString($length = 13, $alpha = true, $num = true)
    {
        $characters = '';

        if ($alpha) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($num) {
            $characters .= '0123456789';
        }

        if (empty($characters)) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return null|string
     */
    public function sanitizeString($string, $delimiter = '_')
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    /**
     * @param string $string
     * @param int $start
     * @param int $length
     * @param string $terminator
     *
     * @return string
     */
    public function trimString($string, $start = 0, $length = 28, $terminator = '...')
    {
        if (strlen($string) > $length) {
            $string = substr($string, $start, $length - strlen($terminator)).$terminator;
        }

        return $string;
    }

    /**
     * @param $file
     *
     * @return array
     */
    public function parseYml($file)
    {
        $array = [];

        if (file_exists($file)) {
            $yml = new Parser();
            $array = $yml->parse(file_get_contents($file));
        }

        return $array;
    }

    /**
     * Convert CSV like output/string to array.
     *
     * @param string $data
     *   CSV like raw output.
     *
     * @param int $index
     *   Start from this row. Default: 6
     *
     * @return array
     */
    public function csvStringToArray($data, $index = 6)
    {
        $i = 0;
        $csv_array = [];
        $raw_data = [];

        $csv_data = str_getcsv($data, "\n");
        foreach ($csv_data as $csv_rows) {
            $raw_data[] = str_getcsv($csv_rows, ",");
        }

        $tmp_array = [];
        foreach ($raw_data as $rows) {
            if ($index == $i) {
                $tmp_array[] = $rows;
                $index++;
            }
            $i++;
        }
        foreach ($tmp_array as $rkey => $rows) {
            foreach ($rows as $fkey => $fvalue) {
                $csv_array[$rkey][$tmp_array[0][$fkey]] = $fvalue;
            }
        }
        unset($csv_array[0]);

        return $csv_array;
    }

    /**
     * Convert size to human readable file size.
     *
     * @param string $size
     *
     * @return string
     */
    public function humanFileSize($size)
    {
        if ($size >= 1073741824) {
            $fileSize = round($size / 1024 / 1024 / 1024).'GB';
        } elseif ($size >= 1048576) {
            $fileSize = round($size / 1024 / 1024).'MB';
        } elseif ($size >= 1024) {
            $fileSize = round($size / 1024).'KB';
        } else {
            $fileSize = (int)$size.' bytes';
        }

        return $fileSize;
    }
}
