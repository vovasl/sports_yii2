<?php

namespace frontend\models\sport;


use backend\models\statistic\total\PlayerTotalSearch;
use common\helpers\TotalHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tn_statistic".
 *
 * @property int $id
 * @property int|null $player_id
 * @property int|null $event_id
 * @property string|null $type
 * @property int|null $min_moneyline
 * @property int|null $profit_0
 * @property int|null $profit_1
 * @property int|null $profit_2
 * @property int|null $profit_3
 * @property int|null $profit_4
 * @property int|null $count_events
 * @property int|null $percent_profit
 * @property int|null $percent_profit_0
 * @property int|null $percent_profit_1
 * @property int|null $percent_profit_2
 * @property int|null $percent_profit_3
 * @property int|null $percent_profit_4
 *
 * @property Event $event
 * @property Player $player
 * @property PlayerTotal $playerTotal
 * @property Odd $odd0
 * @property Odd $odd1
 * @property Odd $odd2
 * @property Odd $odd3
 * @property Odd $odd4
 */
class Statistic extends ActiveRecord
{

    public $count_events;
    public $count_profit_0;
    public $count_profit_1;
    public $count_profit_2;
    public $count_profit_3;
    public $count_profit_4;

    public $percent_profit;
    public $percent_profit_0;
    public $percent_profit_1;
    public $percent_profit_2;
    public $percent_profit_3;
    public $percent_profit_4;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_statistic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'odd_id_0', 'odd_id_1', 'odd_id_2', 'odd_id_3', 'odd_id_4', 'count_events', 'count_profit_0', 'count_profit_1', 'count_profit_2', 'count_profit_3', 'count_profit_4', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['type', 'min_moneyline'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'event_id' => 'Event ID',
            'type' => 'Type',
            'min_moneyline' => 'Moneyline',
            'odd_id_0' => 'Odd ID 0',
            'odd_id_1' => 'Odd ID 1',
            'odd_id_2' => 'Odd ID 2',
            'odd_id_3' => 'Odd ID 3',
            'odd_id_4' => 'Odd ID 4',
            'count_events' => 'Events',
            'profit_0' => 'Profit 0',
            'profit_1' => 'Profit 1',
            'profit_2' => 'Profit 2',
            'profit_3' => 'Profit 3',
            'profit_4' => 'Profit 4',
        ];
    }

    /**
     * Gets query for [[Event]].
     *
     * @return ActiveQuery
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Gets query for [[Player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPlayerTotal(): ActiveQuery
    {
        return $this->hasOne(PlayerTotal::class, ['player_id' => 'player_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd0(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_0']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd1(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_1']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd2(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_2']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd3(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_3']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOdd4(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id_4']);
    }

    /**
     * @return string
     */
    public function getPercentProfit(): string
    {
        return $this->percent_profit / 100;
    }

    /**
     * @return string
     */
    public function getPercentProfit0(): string
    {
        return $this->percent_profit_0 / 100;
    }

    /**
     * @return string
     */
    public function getPercentProfit1(): string
    {
        return $this->percent_profit_1 / 100;
    }

    /**
     * @return string
     */
    public function getPercentProfit2(): string
    {
        return $this->percent_profit_2 / 100;
    }

    /**
     * @return string
     */
    public function getPercentProfit3(): string
    {
        return $this->percent_profit_3 / 100;
    }

    /**
     * @return string
     */
    public function getPercentProfit4(): string
    {
        return $this->percent_profit_4 / 100;
    }

    /**
     * @param string $type
     * @param PlayerTotalSearch $search
     * @return bool
     */
    public function playerTotalButton(string $type, PlayerTotalSearch $search): bool
    {
        /** empty search params */
        if(empty($search->tour) || empty($search->surface) || empty($search->type)) return false;

        switch ($type) {
            case PlayerTotal::ACTION['add']:
                return $this->addPlayerTotalButton($search);
            case PlayerTotal::ACTION['remove']:
                return $this->removePlayerTotalButton($search);
            default:
                return false;
        }
    }

    /**
     * @param PlayerTotalSearch $search
     * @return bool
     */
    private function addPlayerTotalButton(PlayerTotalSearch $search): bool
    {
        /** no player added */
        if(is_null($this->playerTotal)) return true;

        return !($this->playerTotal->tour_id == $search->tour
            && $this->playerTotal->surface_id == $search->surface
            && $this->playerTotal->type == $search->type
        );
    }

    /**
     * @param PlayerTotalSearch $search
     * @return bool
     */
    private function removePlayerTotalButton(PlayerTotalSearch $search): bool
    {
        return (!is_null($this->playerTotal)
            && $this->playerTotal->tour_id == $search->tour
            && $this->playerTotal->surface_id == $search->surface
            && $this->playerTotal->type == $search->type
        );
    }

    public static function add()
    {
        /** get event ids */
        $eventIds = ArrayHelper::getColumn(Event::find()
            ->select(['tn_event.id'])
            ->joinWith(['totalsOver'])
            ->active()
            ->andWhere(['IS NOT', 'tn_event.sofa_id', new Expression('null')])
            ->andWhere(['IS NOT', 'tn_event.pin_id', new Expression('null')])
            ->having(['>', 'count(total_over.id)', 0])
            ->groupBy('tn_event.id')
            ->all(), 'id')
        ;

        /** get total ids */
        $totalIds = ArrayHelper::getColumn(Total::find()
            ->select(['event_id'])
            ->groupBy('event_id')
            ->all(), 'event_id')
        ;

        $ids = array_diff($eventIds, $totalIds);
        if(count($ids) == 0) return;

        /** get events */
        $events = Event::find()
            ->where(['IN', 'tn_event.id', $ids])
            ->joinWith([
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface'
            ])
            //->limit(10)
            ->orderBy(['id' => SORT_ASC])
            ->all()
        ;

        $players = ['home', 'away'];
        $types = ['totalsOver', 'totalsUnder'];
        foreach ($events as $event) {
            if (count($event->totalsOver) == 0) continue;

            /** get moneyline */
            if(!isset($event->homeMoneyline[0]) || !isset($event->awayMoneyline[0])) continue;
            $homeMoneyline = $event->homeMoneyline[0]->odd;
            $awayMoneyline = $event->awayMoneyline[0]->odd;

            /** players */
            foreach ($players as $player) {
                /** types */
                foreach ($types as $type) {
                    /** save model */
                    $model = new Total();
                    $model->player_id = $event->{$player};
                    $model->event_id = $event->id;
                    $model->type = $event->{$type}[0]->add_type;
                    $model->min_moneyline = ($homeMoneyline <= $awayMoneyline) ? $homeMoneyline : $awayMoneyline;
                    $model = self::getProfit($model, $event, $type);
                    $model->save(0);
                }
            }
            //break;
        }
    }

    /**
     * @param Total $model
     * @param Event $event
     * @param string $type
     * @return Total
     */
    public static function getProfit(Total $model, Event $event, string $type): Total
    {
        /** get odds settings */
        $oddsSettings = TotalHelper::ODDS;
        sort($oddsSettings);

        /** get profit values */
        foreach ($event->{$type} as $odd) {
            foreach ($oddsSettings as $k => $setting) {
                if (($k == array_key_last($oddsSettings) && $odd->odd >= $setting) || ($odd->odd >= $setting && $odd->odd < $oddsSettings[$k + 1])) {
                    $profitField = "profit_{$k}";
                    $idField = "odd_id_{$k}";

                    /** field without value */
                    if (is_null($model->{$profitField}) || $k == 0) {
                        $model->{$profitField} = $odd->profit;
                        $model->{$idField} = $odd->id;
                    }

                    break;
                }
            }
        }

        return $model;
    }

}
