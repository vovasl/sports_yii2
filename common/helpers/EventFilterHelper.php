<?php


namespace common\helpers;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\models\statistic\FilterModel;
use frontend\models\sport\Event;

class EventFilterHelper
{

    /**
     * @param array $config
     * @param array $strategies
     * @return array
     */
    public static function totalOverData(array $config, array $strategies): array
    {
        $eventOutput = "";
        $profit = $count = 0;
        $players = [];
        foreach ($strategies as $strategy) {

            $eventOutput .= "<h2>{$strategy['title']}</h2>";
            $models = self::getTotalOver($config, $strategy);
            foreach ($models as $model) {

                /** get profit */
                foreach ($model->odds as $odd) {
                    if(in_array($odd->value, $strategy['values'])) {
                        $eventValue = $odd->value;
                        $eventProfit = $odd->profit;
                        break;
                    }
                }

                /** players stats */
                $players[$model->homePlayer->name]['count']++;
                $players[$model->homePlayer->name]['profit'] += $eventProfit;
                $players[$model->awayPlayer->name]['count']++;
                $players[$model->awayPlayer->name]['profit'] += $eventProfit;

                $profit += $eventProfit;
                $count++;

                /** event output */
                $eventOutput .= EventOutputHelper::total($model, $eventValue, $eventProfit);
            }
        }

        $output = "Events: {$count}<br>";
        $output .= "Profit: {$profit}<hr>";
        $output .= "{$eventOutput}";

        uasort($players, function ($a, $b) {
            return ($a['profit'] > $b['profit'])  ? -1 : 1;
        });

        $data = [
            'output' => $output,
            'players' => $players
        ];

        return $data;
    }

    /**
     * @param array $settings
     * @param array $config
     * @return array
     */
    public static function getTotalOver(array $config, array $settings): array
    {
        $events = Event::find();
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
        $events->andWhere(['IN', 'value', $settings['values']]);

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