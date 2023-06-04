<?php

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use frontend\models\sport\Tournament;

/**
 * TournamentsSearch represents the model behind the search form of `frontend\models\sport\Tournament`.
 */
class TournamentsSearch extends Tournament
{

    public $tour_id;

    public $surface_id;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'surface_id', 'tour_id'], 'integer'],
            [['name', 'comment'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Tournament::find()
            ->with(['events'])
            ->joinWith(['tournamentTour', 'tournamentSurface'])
            ->orderBy([
                Tour::tableName() . '.name' => SORT_ASC,
                Surface::tableName() . '.name' => SORT_ASC,
                'name' => SORT_ASC
            ])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Tour::tableName() . '.id' => $this->tour_id,
            Surface::tableName() . '.id' => $this->surface_id,
        ]);

        $query->andFilterWhere(['like', Tournament::tableName() . '.name', $this->name]);

        return $dataProvider;
    }

}
