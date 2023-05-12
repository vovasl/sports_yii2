<?php
namespace console\controllers;

use Yii;
use backend\components\pinnacle\Pinnacle;
use yii\console\Controller;

class PinnacleController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->Pinnacle->Test();
    }
}