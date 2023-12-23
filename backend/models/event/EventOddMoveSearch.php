<?php

namespace backend\models\event;


use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventOddMoveSearch extends Event
{

    public $tour_id;

    public $surface_id;

    public $tournament_name;

    public $round_id;

    public $player;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['tour_id', 'surface_id', 'round_id', 'away_moneyline_odd', 'odd_move_value_type', 'odd_move_status'], 'integer'],
            [['player', 'tournament_name', 'o_type_name', 'odd_move_value'], 'string'],
            [['home_moneyline_odd'], 'double'],
            [['start_at', 'created'], 'safe'],
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
        $query = Event::find()
            ->select([
                'tn_event.*',
                'home_moneyline.odd home_moneyline_odd',
                'away_moneyline.odd away_moneyline_odd',
                'sp_odd_move.value odd_move_value',
                'sp_odd_move.value_type odd_move_value_type',
                'sp_odd_move.status odd_move_status',
                'sp_odd_type.name o_type_name'
            ])
            ->joinWith([
                'homeMoneyline',
                'awayMoneyline',
                'tournamentRound',
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface',
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                },
                'oddsMove',
                'oddsMove.type',
            ])
            ->where(['IS NOT', 'sp_odd_move.event_id', NULL])
            ->groupBy('sp_odd_move.event_id');
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'start_at' => SORT_DESC
                ],
                'attributes' => [
                    'start_at',
                    'home_moneyline_odd',
                    'away_moneyline_odd',
                    'odd_move_value',
                    'odd_move_status'
                ]
            ],
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere(['IN', Tour::tableName() . '.id', Tour::filterValue($this->tour_id)]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $query->andFilterWhere(['IN', Surface::tableName() . '.id', Surface::filterValue($this->surface_id)]);
        }

        /** round filter */
        if(!is_null($this->round_id)) {
            if($this->round_id == Round::QUALIFIER_FILTER) {
                $query->andFilterWhere(['<>', Round::tableName() . '.id', Round::QUALIFIER]);
            }
            else {
                $query->andFilterWhere([Round::tableName() . '.id' => $this->round_id]);
            }
        }

        /** tournament name filter */
        if(!is_null($this->tournament_name)) {
            $query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->tournament_name]);
        }

        /** event filter */
        if(!is_null($this->player)) {
            $query->andFilterWhere(['or',
                ['like', 'home.name', $this->player],
                ['like', 'away.name', $this->player]
            ]);
        }

        /** moneyline filter */
        if(!empty($this->home_moneyline_odd)) {
            $homeMoneylineOdd = $this->home_moneyline_odd * 100;
            $query->andHaving(['>=', 'home_moneyline_odd', $homeMoneylineOdd]);
            $query->andHaving(['>=', 'away_moneyline_odd', $homeMoneylineOdd]);
        }

        /** odd move filter */
        if(!empty($this->odd_move_value)) {
            preg_match('#(\d.+)(<.*|>.*)#', $this->odd_move_value, $oddMoveValue);
            if(!empty($oddMoveValue)) {
                $query->andFilterWhere([$oddMoveValue[2], 'sp_odd_move.value', (int)$oddMoveValue[1]]);
            }
            else {
                $query->andFilterWhere(['=', 'sp_odd_move.value', $this->odd_move_value]);
            }
        }

        if(!is_null($this->odd_move_value_type)) {
            $query->andFilterWhere(['=', 'sp_odd_move.value_type', $this->odd_move_value_type]);
        }

        /** status filter */
        if(!is_null($this->odd_move_status)) {
            $query->andFilterWhere(['=', 'sp_odd_move.status', $this->odd_move_status]);
        }

        return $dataProvider;
    }

}