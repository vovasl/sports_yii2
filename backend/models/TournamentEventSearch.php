<?php

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Event;
use frontend\models\sport\Player;
use frontend\models\sport\Round;

class TournamentEventSearch extends Event
{

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['round', 'home', 'away', 'total', 'total_games'], 'integer'],
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
            ->withData()
            ->with('odds', 'setsResult')
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
        /*
        $query->andFilterWhere([
            Tour::tableName() . '.id' => $this->tour_id,
            Surface::tableName() . '.id' => $this->surface_id,
        ]);
        */

        //$query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->name]);

        return $dataProvider;
    }

}