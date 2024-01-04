<?php

namespace backend\models\total;


use common\helpers\EventHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\PlayerTotal;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class EventTotalOverSearch extends Event
{

    public $tournament_name;

    public $tour_id;

    public $round_id;

    public $surface_id;

    public $player;

    public $result;

    public $moneyline;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['total', 'total_games', 'round_id', 'result', 'home_result', 'away_result', 'count_odds', 'surface_id', 'tour_id', 'five_sets'], 'integer'],
            [['player', 'tournament_name', 'total_over_value', 'moneyline'], 'string'],
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
        $playerTotal = PlayerTotal::find()->all();
        $events = Event::find()
            ->select('tn_event.id')
            ->withData()
            ->joinWith([
                'homeMoneyline',
                'awayMoneyline',
            ])
            ->where(['<>', Round::tableName() . '.id', Round::QUALIFIER])
            ->andWhere(['IN', 'home', ArrayHelper::getColumn($playerTotal, 'player_id')])
            ->andWhere(['IN', 'away', ArrayHelper::getColumn($playerTotal, 'player_id')])
            ->andWhere(['IN', 'tn_tournament.tour', [1, 3, 8]])
            ->andWhere(['tn_tournament.surface' => 2])
            ->andWhere(['>=', 'home_moneyline.odd', 150])
            ->andWhere(['>=', 'away_moneyline.odd', 150])
            ->all()
        ;

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
            ->where(['IN', 'event.id', ArrayHelper::getColumn($events, 'id')])
            ->groupBy('event.id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'start_at' => SORT_DESC
                ],
                'attributes' => []
            ],
            'pagination' => false
        ]);

        $this->load($params);

        if(!$this->validate()) {
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

        /** tournament name filter */
        if(!empty(trim($this->tournament_name))) {
            $query->andFilterWhere(['like', Tournament::tableName() . '.name', trim($this->tournament_name)]);
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
        if(!empty(trim($this->player))) {
            $query->andFilterWhere(['or',
                ['like', 'home.name', trim($this->player)],
                ['like', 'away.name', trim($this->player)]
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

        /** sets total */
        if(!is_null($this->total)) {
            $query->andFilterWhere([
                'total' => $this->total
            ]);
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['five_sets' => $this->five_sets]);
        }

        return $dataProvider;

    }

}