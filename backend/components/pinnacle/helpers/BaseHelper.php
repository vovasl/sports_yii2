<?php

namespace backend\components\pinnacle\helpers;


class BaseHelper
{
    /**
     * Convert json format to array
     * @param $string
     * @param int $associative
     * @return array
     */
    public static function fromJson($string, $associative = 0) {
        return json_decode($string, $associative);
    }

    /**
     * @param $string
     * @return false|int
     */
    public static function toTime($string)
    {
        return strtotime($string);
    }

    /**
     * Output array
     * @param $array
     */
    public static function outputArray($array)
    {
        echo '<pre>' . print_r($array, 1) . '</pre>';
    }

    /**
     * @param $str
     * @return false|string
     */
    public static function outputDate($str)
    {
        return date('d.m.y H:i', $str);
    }

    /**
     * @return array|false
     */
    public static function getEnv()
    {
        return parse_ini_file('.env');
    }
}