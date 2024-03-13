<?php

namespace backend\services\event;

use frontend\models\sport\Event;
use frontend\models\sport\OddMove;
use Yii;
use yii\db\Exception;
use yii\db\Expression;

class EventCheck
{

    public function process()
    {
        $this->setFavorite();
        $this->setOddMoveStatus();
    }

    public function setFavorite()
    {

    }

    /**
     * @throws Exception
     */
    public function setOddMoveStatus()
    {
        $sql = "UPDATE sp_odd_move 
                LEFT JOIN tn_event ON tn_event.id = sp_odd_move.event_id
                SET sp_odd_move.status = 0
                WHERE sp_odd_move.status = 1 and tn_event.sofa_id is not null
        ";
        Yii::$app->db->createCommand($sql)->execute();
    }

}