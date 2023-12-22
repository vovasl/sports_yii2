<?php

namespace backend\models\event;


use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
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
            [['tour_id', 'surface_id', 'round_id', 'home_moneyline_odd', 'away_moneyline_odd', 'odd_move_value', 'odd_move_status'], 'integer'],
            [['player', 'tournament_name', 'o_type_name'], 'string'],
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

        return $dataProvider;
    }

}