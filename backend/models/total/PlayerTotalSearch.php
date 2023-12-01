<?php

namespace backend\models\total;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use frontend\models\sport\Tour;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PlayerTotalSearch extends Total
{

    public $player_name;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour_id', 'surface_id', 'five_sets', 'count_events', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['type', 'player_name', 'min_moneyline'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['surface_id'], 'exist', 'skipOnError' => true, 'targetClass' => Surface::class, 'targetAttribute' => ['surface_id' => 'id']],
            [['tour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tour::class, 'targetAttribute' => ['tour_id' => 'id']],
        ];
    }

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
                'round(sum(profit_0)/count(event_id)) percent_profit_0',
                'round(sum(profit_1)/count(event_id)) percent_profit_1',
                'round(sum(profit_2)/count(event_id)) percent_profit_2',
                'round(sum(profit_3)/count(event_id)) percent_profit_3',
                'round(sum(profit_4)/count(event_id)) percent_profit_4',
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
                    'percent_profit_0' => SORT_DESC
                ],
                'attributes' => [
                    'player_id',
                    'count_events',
                    'percent_profit_0',
                    'percent_profit_1',
                    'percent_profit_2',
                    'percent_profit_3',
                    'percent_profit_4',
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
        if(is_null($this->five_sets)) {
            $this->five_sets = 0;
        }

        /** player filter */
        if(!empty($this->player_name)) {
            $query->andFilterWhere(['LIKE', 'tn_player.name', $this->player_name]);
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

        /** events filter */
        if(!is_null($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['sp_total.five_sets' => $this->five_sets]);
        }

        return $dataProvider;
    }

}