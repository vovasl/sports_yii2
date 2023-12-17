<?php

namespace backend\models\total;


use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use yii\db\Expression;

class EventTotalSearch extends Event
{

    public $tournament_name;

    public $tour_id;

    public $round_id;

    public $surface_id;

    public $player;

    public $result;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['round', 'home', 'away', 'total', 'total_games', 'round_id', 'result', 'home_result', 'away_result', 'count_odds', 'surface_id', 'tour_id', 'five_sets'], 'integer'],
            [['player', 'tournament_name', 'total_over_value'], 'string'],
            [['home_moneyline_odd'], 'double'],
            [['away'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['away' => 'id']],
            [['home'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['home' => 'id']],
            [['round'], 'exist', 'skipOnError' => true, 'targetClass' => Round::class, 'targetAttribute' => ['round' => 'id']],
            [['tournament'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::class, 'targetAttribute' => ['tournament' => 'id']],
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
                'count(total_over.id) count_odds',
                'avg(total_over.value) total_over_value',
                'home_moneyline.odd home_moneyline_odd',
                'away_moneyline.odd away_moneyline_odd'
            ])
            ->from(['event' => Event::tableName()])
            ->with(['setsResult'])
            ->joinWith([
                'homeMoneyline',
                'awayMoneyline',
                'totalsOver',
                'tournamentRound',
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface',
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                }
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
                    'home_moneyline_odd',
                    'away_moneyline_odd',
                    'total_over_value',
                    'total',
                    'total_games',
                ]
            ],
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** empty search params */
        if(empty($params)) {
            $this->result = 2;
            $this->home_moneyline_odd = 1.5;
            $this->five_sets = 0;
            $this->count_odds = 1;
        }

        if(!is_null($this->tournament_name)) {
            $query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->tournament_name]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'total' => $this->total
        ]);

        if(!is_null($this->round_id)) {
            if($this->round_id == Round::QUALIFIER_FILTER) {
                $query->andFilterWhere(['<>', Round::tableName() . '.id', Round::QUALIFIER]);
            }
            else {
                $query->andFilterWhere([Round::tableName() . '.id' => $this->round_id]);
            }
        }

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

        /** total games filter */
        if(!is_null($this->total_games)) {
            $query->andFilterWhere([
                '>=', 'total_games', $this->total_games
            ]);
        }

        /** count odds filter */
        if(!is_null($this->count_odds)) {
            if($this->count_odds == 1) {
                $query->having(['>', 'count_odds', 0]);
            }
            else if($this->count_odds == -1) {
                $query->having(['count_odds' => 0]);
            }
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere([Tour::tableName() . '.id' => $this->tour_id]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $surface = in_array($this->surface_id, Surface::HARD_INDOOR) ? Surface::HARD_INDOOR : $this->surface_id;
            $query->andFilterWhere(['IN', Surface::tableName() . '.id', $surface]);
        }

        /** total over value filter */
        if(!empty($this->total_over_value)) {
            preg_match('#(\d+)(<.*|>.*)#', $this->total_over_value, $tovalOver);
            if(!empty($tovalOver)) {
                $query->andHaving([$tovalOver[2], 'total_over_value', (int)$tovalOver[1]]);
            }
            else {
                $query->andHaving(['=', 'total_over_value', $this->total_over_value]);
            }
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['five_sets' => $this->five_sets]);
        }

        /** moneyline filter */
        if(!empty($this->home_moneyline_odd)) {
            $homeMoneylineOdd = $this->home_moneyline_odd * 100;
            $query->andHaving(['>=', 'home_moneyline_odd', $homeMoneylineOdd]);
            $query->andHaving(['>=', 'away_moneyline_odd', $homeMoneylineOdd]);
        }

        return $dataProvider;
    }

}