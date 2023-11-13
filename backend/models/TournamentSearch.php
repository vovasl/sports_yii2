<?php

namespace backend\models;


use frontend\models\sport\Event;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Tournament;

/**
 * TournamentsSearch represents the model behind the search form of `frontend\models\sport\Tournament`.
 */
class TournamentSearch extends Tournament
{

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'surface', 'tour', 'count_events'], 'integer'],
            [['name', 'comment'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Tournament::find()
            ->select([
                Tournament::tableName(). '.*',
                'count('.Event::tableName().'.id) count_events'
            ])
            ->joinWith([
                'tournamentTour',
                'tournamentSurface',
                'events'
            ])
            ->groupBy([Tournament::tableName() . '.id']);
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'tour' => SORT_ASC,
                    'surface' => SORT_ASC,
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'tour',
                    'surface',
                    'name',
                    'count_events',
                ]
            ],
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tour' => $this->tour,
            'surface' => $this->surface,
        ]);

        $query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->name]);

        if(!is_null($this->count_events)) {
            $query->andHaving(['>=', 'count_events', $this->count_events]);
        }

        return $dataProvider;
    }

}
