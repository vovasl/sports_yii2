<?php


namespace common\helpers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\statistic\FilterModel;
use frontend\models\sport\Event;

class EventFilterHelper
{

    CONST MONEYLINE_FILTER = [
        'more' => 'more',
        'less' => 'less'
    ];

    CONST EVENTS_STATUS = [
        'FINISHED' => 1,
        'SCHEDULED' => 2
    ];

    /**
     * @param array $settings
     * @return array
     */
    public static function Total(array $settings): array
    {
        $events = Event::find();
        $events->select([
            'tn_event.*',
            'sp_odd.id o_id',
            'sp_odd.add_type o_add_type',
            'sp_odd.value o_value',
            'sp_odd.odd o_odd',
            'sp_odd.profit o_profit',
        ]);
        $events->withData();
        $events->joinWith(['odds' => function($q) use($settings) {
            $q->andOnCondition([
                'type' => 2,
                'add_type' => $settings['add_type'],
            ]);
            return $q;
        }]);

        /** tour filter */
        $events->where(['tour' => $settings['tour']]);

        /** surface filter */
        $events->andWhere(['surface' => $settings['surface']]);

        /** round filter */
        $events->andWhere(['IN', 'round', $settings['rounds']]);

        /** events status filter */
        if($settings['status'] == self::EVENTS_STATUS['FINISHED']) {
            $events->andWhere(['IS NOT', 'tn_event.sofa_id', NULL]);
        }
        else if($settings['status'] == self::EVENTS_STATUS['SCHEDULED']) {
            $events->andWhere(['tn_event.sofa_id' => NULL]);
        }

        /** month filter */
        if($settings['month']) $events->andWhere(['LIKE', 'start_at', $settings['month']]);

        /** value filter */
        $events->andWhere(['sp_odd.value' => $settings['value']]);

        /** odds filter */
        $events->andWhere(['>=', 'sp_odd.odd', $settings['odds']['min']]);
        $events->andWhere(['<', 'sp_odd.odd', $settings['odds']['max']]);

        /** sort */
        $sort = ($settings['sort']) ?: ['start_at' => SORT_DESC];
        $events->orderBy($sort);

        /** events additional filter */
        $models = self::addFilter($events->all(), $settings);

        return $models;
    }

    /**
     * @param array $events
     * @param array $settings
     * @return array
     */
    public static function addFilter(array $events, array $settings): array
    {
        $models = [];
        foreach ($events as $model) {

            /** moneyline filter */
            if(!empty($settings['moneyline']['limit'])) {
                if ($settings['moneyline']['filter'] == self::MONEYLINE_FILTER['more']) {
                    if ($model->homeMoneyline[0]->odd <= $settings['moneyline']['limit'] || $model->awayMoneyline[0]->odd <= $settings['moneyline']['limit']) continue;
                } else if ($settings['moneyline']['filter'] == self::MONEYLINE_FILTER['less']) {
                    if ($model->homeMoneyline[0]->odd > $settings['moneyline']['limit'] && $model->awayMoneyline[0]->odd > $settings['moneyline']['limit']) continue;
                }
            }

            $models[] = $model;
        }

        return $models;
    }

}