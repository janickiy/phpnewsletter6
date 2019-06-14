<?php

namespace App\Helper;

class StringHelpers
{

    /**
     * @param $data
     * @return array
     */
    public static function ObjectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = self::ObjectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * @param int $max
     * @return null|string
     */
    public static function randomText($max = 6)
    {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $size = strlen($chars) - 1;
        $text = null;

        while ($max--)
            $text .= $chars[rand(0, $size)];

        return $text;
    }

    /**
     * @return string
     */
    public static function token()
    {
        return md5(uniqid(rand(), TRUE));
    }

    /**
     * @param $str
     * @param int $chars
     * @return string
     */
    public static function shortText($str, $chars = 500)
    {
        $pos = strpos(substr($str, $chars), " ");
        $srttmpend = strlen($str) > $chars ? '...' : '';

        return substr($str, 0, $chars + $pos) . (isset($srttmpend) ? $srttmpend : '');
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getSetting($key = '')
    {
        $setting = \App\Models\Settings::where('name', $key)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    }

    /**
     * @param $size
     * @param int $maxDecimals
     * @param string $mbSuffix
     * @return string
     */
    public static function formatSizeInMb($size, $maxDecimals = 3, $mbSuffix = "MB")
    {
        $mbSize = round($size / 1024 / 1024, $maxDecimals);
        return preg_replace("/\\.?0+$/", "", $mbSize) . $mbSuffix;
    }

    /**
     * @return mixed
     */
    public static function detectMaxUploadFileSize()
    {
        /**
         * Converts shorthands like "2M" or "512K" to bytes
         *
         * @param int $size
         * @return int|float
         * @throws Exception
         */
        $normalize = function ($size) {
            if (preg_match('/^(-?[\d\.]+)(|[KMG])$/i', $size, $match)) {
                $pos = array_search($match[2], ["", "K", "M", "G"]);
                $size = $match[1] * pow(1024, $pos);
            } else {
                throw new Exception("Failed to normalize memory size '{$size}' (unknown format)");
            }
            return $size;
        };
        $limits = [];
        $limits[] = $normalize(ini_get('upload_max_filesize'));
        if (($max_post = $normalize(ini_get('post_max_size'))) != 0) {
            $limits[] = $max_post;
        }
        if (($memory_limit = $normalize(ini_get('memory_limit'))) != -1) {
            $limits[] = $memory_limit;
        }
        $maxFileSize = min($limits);
        return $maxFileSize;
    }

    /**
     * @return string
     */
    public static function maxUploadFileSize()
    {
        $maxUploadFileSize = self::detectMaxUploadFileSize();

        if (!$maxUploadFileSize or $maxUploadFileSize == 0) {
            $maxUploadFileSize = 2097152;
        }

        return self::formatSizeInMb($maxUploadFileSize);
    }
}