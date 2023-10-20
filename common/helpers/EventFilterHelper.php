<?php


namespace common\helpers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\statistic\FilterModel;
use frontend\models\sport\Event;

class EventFilterHelper
{

    /**
     * @param array $settings
     * @param array $config
     * @return array
     */
    public static function getTotalOver(array $config, array $settings): array
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
        $events->joinWith(['odds' => function($q) use($settings, $config) {
            $q->andOnCondition([
                'type' => 2,
                'add_type' => 'over',
            ]);
            $q->andOnCondition(['>=', 'odd', $settings['odds']['min']]);
            $q->andOnCondition(['<', 'odd', $settings['odds']['max']]);
            if($config['futures']) $q->andOnCondition(['IS', 'profit', NULL]);
            else $q->andOnCondition(['IS NOT', 'profit', NULL]);
            return $q;
        }]);
        $events->where([
            'tour' => $settings['tour'],
            'surface' => $settings['surface'],
        ]);
        $events->andWhere(['value' => $settings['value']]);

        /** round filter */
        $events->andWhere(['IN', 'round', $settings['rounds']]);

        //$events->andWhere(['LIKE', 'start_at', '2023-10-']);
        $events->orderBy([
            'id' => SORT_DESC
        ]);

        $models = [];
        foreach ($events->all() as $model) {

            /** moneyline filter */
            if(!empty($settings['moneyline']['limit'])) {
                if ($settings['moneyline']['filter'] == FilterModel::FILTER['more']) {
                    if ($model->homeMoneyline[0]->odd <= $settings['moneyline']['limit'] || $model->awayMoneyline[0]->odd <= $settings['moneyline']['limit']) continue;
                } else if ($settings['moneyline']['filter'] == FilterModel::FILTER['less']) {
                    if ($model->homeMoneyline[0]->odd > $settings['moneyline']['limit'] && $model->awayMoneyline[0]->odd > $settings['moneyline']['limit']) continue;
                }
            }

            $models[] = $model;
        }

        return $models;
    }

}