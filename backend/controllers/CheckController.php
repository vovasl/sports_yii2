<?php

namespace backend\controllers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\services\CheckPlayer;
use frontend\models\sport\Player;
use frontend\models\sport\PlayerAdd;
use yii\filters\AccessControl;
use yii\web\Controller;

class CheckController extends Controller
{

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionEvent(): string
    {
        return $this->render('event');
    }

    /**
     * @return string
     */
    public function actionPlayer(): string
    {
        return $this->render('player', [
            'add' => CheckPlayer::add()
        ]);
    }

}