<?php


namespace common\helpers;


class PlayerHelper
{

    /**
     * @param array $models
     * @param $value
     * @return array
     */
    public static function getStatsByOddVal(array $models, $value): array
    {
        $fields = ['homePlayer', 'awayPlayer'];
        $players = [];
        foreach ($models as $model) {

            /** get odd */
            $odd = OddHelper::getOddByVal($model, $value);

            /** player stats */
            foreach ($fields as $field) {
                $player = $model->{$field}->name;
                $players[$player]['count']++;
                $players[$player]['profit'] += $odd->profit;
            }

        }

        /** sort */
        uasort($players, function ($a, $b) {
            return ($a['profit'] > $b['profit'])  ? -1 : 1;
        });

        return $players;
    }

}