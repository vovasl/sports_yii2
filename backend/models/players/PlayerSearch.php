<?php

namespace backend\models\players;


use frontend\models\sport\Event;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Player;
use yii\db\Expression;

/**
 * PlayerSearch represents the model behind the search form of `frontend\models\sport\Player`.
 */
class PlayerSearch extends Player
{

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'type', 'sofa_id', 'count_events'], 'integer'],
            [['name', 'birthday', 'plays', 'comment'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Player::find();
        $query
            ->select(['players.*', 'count(events.id) count_events'])
            ->from(['players' => Player::tableName()])
            ->with([
                'homeEvents',
                'awayEvents',
                'homeEvents.eventTournament',
                'awayEvents.eventTournament'
            ])
            ->join('JOIN',Event::tableName() . " events", "events.home = players.id OR events.away = players.id")
            ->groupBy('players.id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['count_events' => SORT_DESC],
                'attributes' => [
                    'name',
                    'count_events'
                ]
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        if(!is_null($this->sofa_id)) {
            if($this->sofa_id == 1) $query->andFilterWhere(['IS NOT', 'players.sofa_id', new Expression('null')]);
            else if($this->sofa_id == 2) $query->andFilterWhere(['IS', 'players.sofa_id', new Expression('null')]);
        }

        if(!empty($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        return $dataProvider;
    }
}
