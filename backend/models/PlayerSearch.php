<?php


namespace backend\models;

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
            [['id', 'type', 'sofa_id'], 'integer'],
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
        $query->with(['homeEvents', 'awayEvents']);

        // add conditions that should always apply here

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
            'id' => $this->id,
            'type' => $this->type,
            'birthday' => $this->birthday,
            'sofa_id' => $this->sofa_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'plays', $this->plays])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
