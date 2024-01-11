<?php

namespace backend\services;


use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\OddType;
use frontend\models\sport\Statistic;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class EventStatisticSave
{

    CONST PLAYER_TYPE = [
        'home',
        'away'
    ];

    CONST METHODS = [
        'totals' => [
            'totalsOver',
            'totalsUnder'
        ],
        //'moneyline' => [],
    ];

    private $event;

    /**
     * @return bool
     */
    public function init(): bool
    {
        /** get event ids */
        $ids = $this->getEventIds();
        if(count($ids) == 0) return true;

        /** get events */
        $events = $this->getEvents($ids);

        foreach ($events as $event) {
            $this->event = $event;

            /** add statistic */
            $this->add();
            //break;
        }

        return true;
    }

    /**
     * @return array
     */
    private function getEventIds(): array
    {
        /** get event ids */
        $eventIds = ArrayHelper::getColumn(Event::find()
            ->select(['tn_event.id'])
            ->active()
            ->andWhere(['IS NOT', 'tn_event.sofa_id', new Expression('null')])
            ->andWhere(['IS NOT', 'tn_event.pin_id', new Expression('null')])
            ->groupBy('tn_event.id')
            ->all(), 'id')
        ;

        /** get statistic event ids */
        $statisticIds = ArrayHelper::getColumn(Statistic::find()
            ->select(['event_id'])
            ->groupBy('event_id')
            ->all(), 'event_id')
        ;

        return array_diff($eventIds, $statisticIds);
    }

    /**
     * @return array|Event[]
     */
    private function getEvents(array $ids): array
    {
        /** get events */
        return Event::find()
            ->where(['IN', 'tn_event.id', $ids])
            ->joinWith([
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface'
            ])
            ->orderBy(['tn_event.id' => SORT_ASC])
            //->limit(10)
            ->all();
    }

    /**
     * @return bool
     */
    private function add(): bool
    {
        foreach (self::METHODS as $type => $methods) {

            /** get type id */
            $oddType = OddType::findOne(['name' => $type]);
            if(is_null($oddType)) continue;

            /** add statistic */
            $addMethod = $this->getAddMethod($type);
            $this->$addMethod($type, $oddType->id);
        }

        return true;
    }

    /**
     * @param string $type
     * @return string
     */
    private function getAddMethod(string $type): string
    {
        return "add" . ucfirst($type);
    }

    private function addMoneyline(string $type, int $typeId): bool
    {
        return true;
    }

    /**
     * @param string $type
     * @param int $typeId
     * @return bool
     */
    private function addTotals(string $type, int $typeId): bool
    {
        if (count($this->event->totalsOver) == 0) return false;

        /** get moneyline */
        if(!isset($this->event->homeMoneyline[0]) || !isset($this->event->awayMoneyline[0])) return false;
        $moneyline = [
            'home' => $this->event->homeMoneyline[0]->odd,
            'away' => $this->event->awayMoneyline[0]->odd
        ];

        /** players */
        foreach (self::PLAYER_TYPE as $player) {
            /** types */
            foreach (self::METHODS[$type] as $method) {
                /** save model */
                $model = new Statistic();
                $model->player_id = $this->event->{$player};
                $model->event_id = $this->event->id;
                $model->type = $typeId;
                $model->add_type = $this->event->{$method}[0]->add_type;
                $model->min_moneyline = ($moneyline['home'] <= $moneyline['away']) ? $moneyline['home'] : $moneyline['away'];
                $model = $this->getTotalProfit($model, $method);
                $model->save(0);
            }
        }

        return true;
    }

    /**
     * @param Statistic $model
     * @param string $method
     * @return Statistic
     */
    private function getTotalProfit(Statistic $model, string $method): Statistic
    {
        /** get odds settings */
        $oddsSettings = TotalHelper::ODDS;
        sort($oddsSettings);

        /** get profit values */
        foreach ($this->event->{$method} as $odd) {
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