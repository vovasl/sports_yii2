<?php

namespace backend\models;


use frontend\models\sport\Tournament;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use yii\db\Expression;

class TournamentEventSearch extends Event
{

    public $tournament_name;

    public $round_id;

    public $player;

    public $result;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['round', 'home', 'away', 'total', 'total_games', 'round_id', 'result', 'home_result', 'away_result'], 'integer'],
            [['player', 'tournament_name'], 'string'],
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
     * @param $id
     * @return ActiveDataProvider
     */
    public function search(array $params, $id = null): ActiveDataProvider
    {
        $query = Event::find()
            ->from(['event' => 'tn_event'])
            ->with(['odds', 'setsResult'])
            ->joinWith([
                'tournamentRound',
                'eventTournament',
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                }
            ])
            //->where(['tournament' => $id])
        ;

        if(!is_null($id)) {
            $query->where(['tournament' => $id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['start_at' => SORT_DESC],
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
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(is_null($id) && !is_null($this->tournament_name)) {
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

        if(!is_null($this->total_games)) {
            $query->andFilterWhere([
                '>=', 'total_games', $this->total_games
            ]);
        }

        return $dataProvider;
    }

}