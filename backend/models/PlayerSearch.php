<?php


namespace backend\models;

use frontend\models\sport\Event;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Player;

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
            ->with(['homeEvents', 'awayEvents', 'homeEvents.eventTournament', 'awayEvents.eventTournament'])
            ->leftJoin(Event::tableName() . " events", "events.home = players.id OR events.away = players.id")
            ->groupBy('players.id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['count_events' => SORT_DESC],
                'attributes' => [
                    'name',
                    'sofa_id',
                    'count_events'
                ]
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'birthday' => $this->birthday,
            'sofa_id' => $this->sofa_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'plays', $this->plays])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        if(!is_null($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        return $dataProvider;
    }
}
