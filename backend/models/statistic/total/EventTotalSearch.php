<?php

namespace backend\models\statistic\total;

use common\helpers\EventHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Statistic;
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

    public $tournament_id;

    public $player;

    public $result;

    public $moneyline;

    public $favorite;

    public $event_ids;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['total', 'total_games', 'round_id', 'result', 'home_result', 'away_result', 'count_odds', 'surface_id', 'tour_id', 'five_sets', 'total_over_min_profit', 'tournament_id'], 'integer'],
            [['player', 'tournament_name', 'total_avg_value', 'moneyline', 'favorite'], 'string'],
            ['event_ids', 'each', 'rule' => ['integer']],
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
                'avg(total_over.value) total_avg_value',
                'max(total_over.profit) total_over_min_profit',
                'home_moneyline.odd home_moneyline_odd',
                'away_moneyline.odd away_moneyline_odd'
            ])
            ->from(['event' => Event::tableName()])
            ->with([
                'setsResult',
                'totalOverStat',
                'totalUnderStat'
            ])
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
            ->where(['event.status' => 1])
            ->having(['>', 'count_odds', 0])
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
                    'tour_id' => [
                        'asc' => [Tour::tableName() . '.name' => SORT_ASC],
                        'desc' => [Tour::tableName() . '.name' => SORT_DESC],
                    ],
                    'surface_id' => [
                        'asc' => [Surface::tableName() . '.name' => SORT_ASC],
                        'desc' => [Surface::tableName() . '.name' => SORT_DESC],
                    ],
                    'tournament_name' => [
                        'asc' => [Tournament::tableName() . '.name' => SORT_ASC],
                        'desc' => [Tournament::tableName() . '.name' => SORT_DESC],
                    ],
                    'round_id' => [
                        'asc' => [Round::tableName() . '.rank' => SORT_ASC, 'event.start_at' => SORT_DESC],
                        'desc' => [Round::tableName() . '.rank' => SORT_DESC, 'event.start_at' => SORT_ASC],
                    ],
                    'five_sets',
                    'total_avg_value',
                    'total',
                    'total_games',
                    'total_over_min_profit',
                ]
            ],
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** default search params */
        if(empty($params)) {
            $this->tour_id = -1;
            $this->round_id = Round::MAIN;
            $this->moneyline = Statistic::TOTAL_FILTER['moneyline']['equal'];
            $this->result = 2;
            $this->count_odds = 1;
        }

        /** tour filter */
        if(!is_null($this->tour_id)) {
            $query->andFilterWhere(['IN', Tour::tableName() . '.id', Tour::getValue($this->tour_id)]);
        }

        /** surface filter */
        if(!is_null($this->surface_id)) {
            $query->andFilterWhere(['IN', Surface::tableName() . '.id', Surface::filterValue($this->surface_id)]);
        }

        /** tournament id filter */
        if(!is_null($this->tournament_id)) {
            $query->andFilterWhere(['event.tournament' => $this->tournament_id]);
        }

        /** tournament name filter */
        if(!empty(trim($this->tournament_name))) {
            $query->andFilterWhere(['like', Tournament::tableName() . '.name', trim($this->tournament_name)]);
        }

        /** round filter */
        if (!is_null($this->round_id)) {
            $query->andFilterWhere(['IN', Round::tableName() . '.id', Round::filterValue($this->round_id)]);
        }

        /** event filter */
        if(!empty(trim($this->player))) {
            $query->andFilterWhere(['or',
                ['like', 'home.name', trim($this->player)],
                ['like', 'away.name', trim($this->player)]
            ]);
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

        /** favorite filter */
        if(!empty($this->favorite)) {
            if($player = Player::find()->where(['name' => $this->player])->one()) {
                if ($this->favorite == 'Yes') {
                    $query->andFilterWhere(['event.favorite' => $player->id]);
                } else if ($this->favorite == 'No') {
                    $query->andFilterWhere(['!=', 'event.favorite', $player->id]);
                }
            }
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

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['five_sets' => $this->five_sets]);
        }

        /** average total value filter */
        if(!empty($this->total_avg_value)) {
            $totalOver = EventHelper::parseValueFilter($this->total_avg_value);
            if(!empty($totalOver)) {
                $query->andHaving([$totalOver[2], 'total_avg_value', $totalOver[1]]);
            }
            else {
                $query->andHaving(['=', 'total_avg_value', $this->total_avg_value]);
            }
        }

        /** total sets filter */
        if(!is_null($this->total)) {
            $query->andFilterWhere([
                'total' => $this->total
            ]);
        }

        /** total games filter */
        if(!is_null($this->total_games)) {
            $query->andFilterWhere([
                '>=', 'total_games', $this->total_games
            ]);
        }


        /** min total over filter */
        if(!is_null($this->total_over_min_profit)) {
            switch ($this->total_over_min_profit) {
                case 1:
                    $query->andHaving(['>', 'total_over_min_profit', 0]);
                    break;
                case 100:
                    $query->andHaving(['=', 'total_over_min_profit', 0]);
                    break;
                case -1:
                    $query->andHaving(['<', 'total_over_min_profit', 0]);
                    break;
                default:
                    break;
            }
        }

        /** count odds filter */
        if(!is_null($this->count_odds)) {
            if($this->count_odds == 1) {
                $query->andHaving(['>', 'count_odds', 0]);
            }
            else if($this->count_odds == -1) {
                $query->andHaving(['count_odds' => 0]);
            }
        }

        /** event ids filter */
        if(!is_null($this->event_ids)) {
            $query->andWhere(['IN', 'event.id', $this->event_ids]);
        }


        /** custom filters */
/*        $disableFavoritePlayers = [239 ,323, 227, 228, 463, 442];
        $favoritePlayers = [235, 225, 438, 233, 465, 240, 453, 237, 291, 433, 443, 448];

        $favoritePlayers = $disableFavoritePlayers;

        $query->andFilterWhere(['or',
            ['IN', 'event.home', $favoritePlayers],
            ['IN', 'event.away', $favoritePlayers]
        ]);*/

        return $dataProvider;
    }

}