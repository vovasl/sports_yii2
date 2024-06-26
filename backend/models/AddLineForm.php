<?php

namespace backend\models;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddType;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AddLineForm extends Model
{

    CONST PIN_ID = 1;

    public $event_id;
    public $type;
    public $add_type;
    public $player_id;
    public $value;
    public $odd_home;
    public $odd_away;
    public $odd_over;
    public $odd_under;

    public $close;
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['event_id', 'type'], 'required'],
            [['event_id', 'type', 'player_id', 'close'], 'integer'],
            [['add_type', 'value', 'odd_home', 'odd_away', 'odd_over', 'odd_under'], 'string'],
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
            'odd_home' => 'Home',
            'odd_away' => 'Away',
            'odd_over' => 'Over',
            'odd_under' => 'Under',
            'close' => 'Close'
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
            ->where([
                'event.status' => 1,
                'event.pin_id' => null
            ])
            ->groupBy('event.id')
            ->orderBy(['event.start_at' => SORT_DESC])
            //->having(['count_odds' => 0])
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

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        if(empty($this->event_id)) return [];

        $event = Event::findOne($this->event_id);
        return $event->dropdownPlayers();
    }

    public function prepare()
    {
        $this->odd_home = null;
        $this->odd_away = null;
        $this->odd_under = null;
        $this->odd_over = null;

        /** totals */
        if(in_array($this->type, [Odd::TYPE['spreads'], Odd::TYPE['totals']])) {
            $this->value += 0.5;
        }
    }

}