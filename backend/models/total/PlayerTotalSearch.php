<?php

namespace backend\models\total;


use frontend\models\sport\Odd;
use frontend\models\sport\Total;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PlayerTotalSearch extends Total
{

    /**
     * @return array
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Total::find()
            ->select([
                'sp_total.*',
                'count(event_id) count_events',
                'sum(profit_0) sum_profit_0',
                'sum(profit_1) sum_profit_1',
                'sum(profit_2) sum_profit_2',
                'sum(profit_3) sum_profit_3',
                'sum(profit_4) sum_profit_4',
            ])
            ->joinWith([
                'player',
                'surface',
                'tour',
            ])
            ->groupBy('player_id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sum_profit_0' => SORT_DESC
                ],
                'attributes' => [
                    'player_id',
                    'count_events',
                    'sum_profit_0',
                    'sum_profit_1',
                    'sum_profit_2',
                    'sum_profit_3',
                    'sum_profit_4',
                ]
            ],
            /*
            'pagination' => [
                'pageSize' => 100,
            ],
            */
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** default values */
        if(is_null($this->type)) {
            $this->type = Odd::ADD_TYPE['over'];
        }
        if(is_null($this->count_events)) {
            $this->count_events = 10;
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere(['sp_total.tour_id' => $this->tour_id]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $surface = in_array($this->surface_id, [2, 4]) ? [2, 4] : $this->surface_id;
            $query->andFilterWhere(['IN', 'sp_total.surface_id', $surface]);
        }

        /** type filter */
        if(!is_null($this->type)) {
            $query->andFilterWhere(['sp_total.type' => $this->type]);
        }

        /** moneyline filter */
        if(!empty($this->min_moneyline)) {
            preg_match('#(\d.+)(<.*|>.*)#', $this->min_moneyline, $minMoneyline);
            if(!empty($minMoneyline)) {
                $minMoneyline[1] = $minMoneyline[1] * 100;
                $query->andFilterWhere([$minMoneyline[2], 'min_moneyline', (int)$minMoneyline[1]]);
            }
            else {
                $query->andFilterWhere(['=', 'min_moneyline', $this->min_moneyline]);
            }
        }

        /** events filter */
        if(!is_null($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        return $dataProvider;
    }

}