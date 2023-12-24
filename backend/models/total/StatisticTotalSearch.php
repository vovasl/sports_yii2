<?php

namespace backend\models\total;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use frontend\models\sport\Tour;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatisticTotalSearch extends Total
{

    public $tour;
    public $surface;
    public $round;
    public $five_sets;
    public $value0;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour', 'surface', 'round', 'five_sets', 'count_events', 'count_profit_0', 'count_profit_1', 'count_profit_2', 'count_profit_3', 'count_profit_4', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['type', 'min_moneyline', 'value0'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
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
            ])
            ->joinWith([
                'event',
                'event.eventTournament.tournamentTour',
                'event.eventTournament.tournamentSurface',
                'player',
                'odd0' => function ($q) {
                    $q->from(Odd::tableName() . ' odd0');
                },
            ])
        ;
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

        /** default search params */
        if(empty($params)) {
            $this->type = Odd::ADD_TYPE['over'];
            $this->min_moneyline = '1.5>=';
            $this->round = 100;
        }

        /** tour filter */
        if(!is_null($this->tour)) {
            $query->andFilterWhere(['IN', 'tn_tour.id', Tour::filterValue($this->tour)]);
        }

        /** surface filter */
        if(!is_null($this->surface)) {
            $query->andFilterWhere(['IN', 'tn_surface.id', Surface::filterValue($this->surface)]);
        }

        /** round filter */
        if(!is_null($this->round)) {
            if($this->round == Round::QUALIFIER_FILTER) {
                $query->andFilterWhere(['<>', 'tn_event.round', Round::QUALIFIER]);
            }
            else {
                $query->andFilterWhere(['tn_event.round' => $this->round]);
            }
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
            $query->andFilterWhere(['tn_event.five_sets' => $this->five_sets]);
        }

        /** value0 filter */
        if(!empty($this->value0)) {
            preg_match('#(\d.+)(<.*|>.*)#', $this->value0, $value);
            if(!empty($value)) {
                $query->andFilterWhere([$value[2], 'odd0.value', (int)$value[1]]);
            }
            else {
                $query->andFilterWhere(['odd0.value' => $this->value0]);
            }
        }

        return $dataProvider;
    }

}