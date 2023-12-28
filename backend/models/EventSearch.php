<?php

namespace backend\models;


use common\helpers\EventHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use yii\db\Expression;

class EventSearch extends Event
{

    public $tournament_name;

    public $tour_id;

    public $round_id;

    public $surface_id;

    public $player;

    public $result;

    public $count_odds;

    public $moneyline;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['tour_id', 'surface_id', 'round_id', 'result', 'count_odds'], 'integer'],
            [['tournament_name', 'player', 'moneyline'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Event::find()
            ->select([
                'event.*',
                'count(sp_odd.id) count_odds',
                'home_moneyline.odd home_moneyline_odd',
                'away_moneyline.odd away_moneyline_odd'
            ])
            ->from(['event' => Event::tableName()])
            ->with(['setsResult'])
            ->joinWith([
                'odds',
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
            ])
            ->groupBy('event.id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'start_at' => empty($params) ? SORT_ASC : SORT_DESC
                ],
                'attributes' => [
                    'start_at',
                    'round_id' => [
                        'asc' => [Round::tableName() . '.rank' => SORT_ASC, 'event.start_at' => SORT_DESC],
                        'desc' => [Round::tableName() . '.rank' => SORT_DESC, 'event.start_at' => SORT_ASC],
                    ],
                    'total',
                    'total_games'
                ]
            ],
            //'pagination' => false,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** empty search params */
        if(empty($params)) {
            $this->result = 2;
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere(['IN', Tour::tableName() . '.id', Tour::filterValue($this->tour_id)]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $query->andFilterWhere(['IN', Surface::tableName() . '.id', Surface::filterValue($this->surface_id)]);
        }

        /** tournament name filter */
        if(!is_null($this->tournament_name)) {
            $query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->tournament_name]);
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

        /** event filter */
        if(!is_null($this->player)) {
            $query->andFilterWhere(['or',
                ['like', 'home.name', $this->player],
                ['like', 'away.name', $this->player]
            ]);
        }

        /** result filter */
        if(!is_null($this->result)) {
            if($this->result == 1) {
                $query->andFilterWhere(['IS NOT', 'home_result', new Expression('null')]);
                $query->andFilterWhere(['IS NOT', 'away_result', new Expression('null')]);
            }
            else if($this->result == 2) {
                $query->andFilterWhere(['IS', 'home_result', new Expression('null')]);
                $query->andFilterWhere(['IS', 'away_result', new Expression('null')]);
            }
        }

        /** moneyline filter */
        if(!empty($this->moneyline)) {
            $moneyline = EventHelper::parseValueFilter($this->moneyline);
            if(!empty($moneyline)) {

                /** get odd */
                $moneylineOdd = Odd::setOdd($moneyline[1]);

                /** get condition */
                $condition = (strpos($moneyline[2], '>') !== false) ? 'AND' : 'OR';
                $query->andHaving([$condition,
                    [$moneyline[2], 'home_moneyline_odd', $moneylineOdd],
                    [$moneyline[2], 'away_moneyline_odd', $moneylineOdd]
                ]);
            }
            else {

                /** get odd */
                $moneylineOdd = Odd::setOdd($this->moneyline);

                $query->andHaving(['OR',
                    ['=', 'home_moneyline_odd', $moneylineOdd],
                    ['=', 'away_moneyline_odd', $moneylineOdd]
                ]);
            }
        }

        /** odds filter */
        if(!is_null($this->count_odds)) {
            if($this->count_odds == 1) {
                $query->andFilterWhere(['IS NOT', 'pin_id', new Expression('null')]);
                $query->having(['>', 'count_odds', 0]);
            }
            else if($this->count_odds == -1) {
                $query->andFilterWhere(['IS NOT', 'pin_id', new Expression('null')]);
                $query->having(['count_odds' => 0]);
            }
            else if($this->count_odds == -2) { // finished
                $query->andFilterWhere(['status' => 1]);
                $query->andFilterWhere(['IS NOT', 'event.sofa_id', new Expression('null')]);
                $query->having(['count_odds' => 0]);
            }
        }

        return $dataProvider;
    }

}