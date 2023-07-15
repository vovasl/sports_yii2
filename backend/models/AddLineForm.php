<?php

namespace backend\models;


use frontend\models\sport\Event;
use frontend\models\sport\OddType;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AddLineForm extends Model
{
    public $event_id;
    public $type;
    public $add_type;
    public $player_id;
    public $value;
    public $odd;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['event_id', 'type', 'odd'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'event_id' => 'Event',
            'type' => 'Type',
            'add_type' => 'Additional type',
            'player_id' => 'Player',
            'value' => 'Value',
            'odd' => 'Odd'
        ];
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return ArrayHelper::map(Event::find()
            ->select(['event.*', 'count(sp_odd.id) count_odds'])
            ->from(['event' => Event::tableName()])
            ->with(['eventTournament', 'tournamentRound', 'homePlayer', 'awayPlayer'])
            ->leftJoin('sp_odd', 'sp_odd.event = event.id')
            ->where(['event.status' => 1])
            ->groupBy('event.id')
            ->orderBy(['event.start_at' => SORT_DESC])
            ->having(['count_odds' => 0])
            ->all()
        , 'id', 'fullInfo');
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return array_map('ucfirst', ArrayHelper::map(OddType::find()
            ->all()
        , 'id','name'));
    }

}