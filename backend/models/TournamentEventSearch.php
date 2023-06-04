<?php

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;

class TournamentEventSearch extends Event
{

    public $round_id;

    public $player;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['round', 'home', 'away', 'total', 'total_games', 'round_id'], 'integer'],
            [['player'], 'string'],
            [['away'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['away' => 'id']],
            [['home'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['home' => 'id']],
            [['round'], 'exist', 'skipOnError' => true, 'targetClass' => Round::class, 'targetAttribute' => ['round' => 'id']],
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
    public function search(array $params, $id): ActiveDataProvider
    {
        $query = Event::find()
            ->from(['event' => 'tn_event'])
            ->with(['odds', 'setsResult'])
            ->joinWith([
                'tournamentRound',
                'homePlayer' => function($q) {
                    $q->from(Player::tableName() . ' home');
                },
                'awayPlayer' => function($q) {
                    $q->from(Player::tableName() . ' away');
                }
            ])
            ->where(['tournament' => $id])
            ->orderTournament()
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

        $query->andFilterWhere([
            Round::tableName() . '.id' => $this->round_id,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'home.name', $this->player],
            ['like', 'away.name', $this->player]
        ]);

        return $dataProvider;
    }

}