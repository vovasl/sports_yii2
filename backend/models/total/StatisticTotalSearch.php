<?php


namespace backend\models\total;


use frontend\models\sport\Odd;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatisticTotalSearch extends Total
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
                'round(count(profit_0)/2) count_profit_0',
                'round(count(profit_1)/2) count_profit_1',
                'round(count(profit_2)/2) count_profit_2',
                'round(count(profit_3)/2) count_profit_3',
                'round(count(profit_4)/2) count_profit_4',
                'round(sum(profit_0)/count(profit_0), 1) percent_profit_0',
                'round(sum(profit_1)/count(profit_1), 1) percent_profit_1',
                'round(sum(profit_2)/count(profit_2), 1) percent_profit_2',
                'round(sum(profit_3)/count(profit_3), 1) percent_profit_3',
                'round(sum(profit_4)/count(profit_4), 1) percent_profit_4',
            ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                ]
            ],
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
        if(is_null($this->five_sets)) {
            $this->five_sets = 0;
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere(['sp_total.tour_id' => $this->tour_id]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $surface = in_array($this->surface_id, Surface::HARD_INDOOR) ? Surface::HARD_INDOOR : $this->surface_id;
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

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['sp_total.five_sets' => $this->five_sets]);
        }

        return $dataProvider;
    }

}