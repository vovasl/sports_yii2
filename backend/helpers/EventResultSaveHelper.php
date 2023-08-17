<?php

namespace backend\helpers;


use yii\helpers\Html;

class EventResultSaveHelper
{

    /**
     * @param string $message
     * @return string
     */
    public static function errorMsg(string $message): string
    {
        return "<br><span style='color: red;'>{$message}</span>";
    }

    /**
     * @param string $message
     * @return string
     */
    public static function warningMsg(string $message): string
    {
        return "<br><span style='color: coral;'>{$message}</span>";
    }

    /**
     * @param $id
     * @return string
     */
    public static function getLink($id): string
    {
        return Html::a('Link', ['/event/view', 'id' => $id], ['target'=>'_blank']);
    }

    /**
     * @param $id
     * @return string
     */
    public static function getEditLink($id): string
    {
        return Html::a('Edit', ['/event/update', 'id' => $id], ['target'=>'_blank']);
    }

}