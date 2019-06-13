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

}