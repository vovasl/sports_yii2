<?php

namespace backend\components\pinnacle\helpers;


use backend\components\pinnacle\Pinnacle;

class BaseHelper
{

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
        return date('Y.m.d H:i', $str);
    }

    /**
     * @param $events
     * @return string
     */
    public static function events($events, $method): string
    {
        $output = "";
        foreach ($events as $event) {
            $output .= "{$event['tournament']} {$event['round']} <br>";
            $output .= "{$event['o_starts']} {$event['home']} - {$event['away']} <br>";
            $output .= self::{$method}($event['odds']);
            $output .= "<hr>";
        }

        return $output;
    }

    /**
     * @param $odds
     * @return string
     */
    public static function tennis($odds): string
    {
        $output = "<br>";
        foreach($odds as $type => $period) {
            $output .= ucfirst($type) . "<br>";
            foreach(Pinnacle::TENNIS_CONFIG[$type] as $line) {
                $output .= "{$line} ({$period["o_{$line}UpdatedAt"]}) <br>";
                $output .= self::tennisLine($period[$line]);
                $output .= "<br>";
            }
        }

        return $output;
    }

    /**
     * @param $line
     * @return string
     */
    public static function tennisLine($line): string
    {
        $rows = "";
        if(!is_array($line)) return $rows;
        foreach ($line as $val) {
            if(is_array($val)) {
                if(count($val) == 3) $rows .= implode($val, " ") . "<br>";
                else { // ::log  remove fields
                }
            }
            else $rows .= "$val ";
        }
        if(!empty($val) && !is_array($val)) $rows .= "<br>";
        return $rows;
    }

}