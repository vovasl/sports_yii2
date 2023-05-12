<?php
namespace console\controllers;


use Yii;
use yii\console\Controller;

class PinnacleController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->Pinnacle->run();
        //Pinnacle::TestStatic();
    }
}