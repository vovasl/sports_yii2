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

class PlayerTotalSearch extends Total
{

    public $player_name;
    public $tour;
    public $surface;
    public $round;
    public $five_sets;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour', 'surface', 'round', 'five_sets', 'count_events', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['type', 'player_name', 'min_moneyline'], 'string', 'max' => 255],
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
                'count(event_id) count_events',
                'round(sum(profit_0)/count(profit_0)) percent_profit_0',
                'round(sum(profit_1)/count(profit_1)) percent_profit_1',
                'round(sum(profit_2)/count(profit_2)) percent_profit_2',
                'round(sum(profit_3)/count(profit_3)) percent_profit_3',
                'round(sum(profit_4)/count(profit_4)) percent_profit_4',
            ])
            ->joinWith([
                'event',
                'event.eventTournament.tournamentTour',
                'event.eventTournament.tournamentSurface',
                'player',
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

        /** default search params */
        if(empty($params)) {
            $this->type = Odd::ADD_TYPE['over'];
            $this->min_moneyline = '1.5>=';
            $this->count_events = 15;
            $this->round = 100;
        }

        /** player filter */
        if(!empty(trim($this->player_name))) {
            $query->andFilterWhere(['LIKE', 'tn_player.name', trim($this->player_name)]);
            $this->count_events = null;
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

        /** events filter */
        if(!is_null($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['tn_event.five_sets' => $this->five_sets]);
        }

        return $dataProvider;
    }

}