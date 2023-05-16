<?php

namespace frontend\controllers;


use yii\web\Controller;

class EventController extends Controller
{

    /**
     * @return string
     */
    public function actionMoneyline()
    {
       return $this->render('moneyline');
    }

}