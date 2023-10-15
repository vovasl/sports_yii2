<?php

namespace backend\models\statistic;


use frontend\models\sport\query\EventQuery;
use frontend\models\sport\Round;
use yii\base\Model;

class FilterModel extends Model
{

    public $tour;
    public $surface;
    public $round = 0;
    public $five_sets = 0;

    /**
     * FilterModel constructor.
     * @param $params
     * @param array $config
     */
    public function __construct($params, array $config = [])
    {
        if(isset($params['FilterModel'])) {
            $filter = $params['FilterModel'];
            $this->tour = !empty($filter['tour']) ? $filter['tour'] : null;
            $this->surface = !empty($filter['surface']) ? $filter['surface'] : null;
            $this->round = $filter['round'];
            $this->five_sets = $filter['five_sets'];
        }
        parent::__construct($config);
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['tour', 'surface', 'round', 'five_sets'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'tour' => 'Tour',
            'surface' => 'Surface',
            'round' => 'Round',
            'five_sets' => 'Five Sets',
        ];
    }


    /**
     * @param EventQuery $q
     * @return EventQuery
     */
    public function searchEvents(EventQuery $q): EventQuery
    {

        /** tour filter */
        if(!empty($this->tour)) $q->andWhere(['tn_tour.id' => $this->tour]);

        /** surface filter */
        if(!empty($this->surface)) $q->andWhere(['tn_tournament.surface' => $this->surface]);

        /** round filter */
        if($this->round) {
            if($this->round == Round::QUALIFIER_FILTER) {
                $q->andFilterWhere(['<>', Round::tableName() . '.id', Round::QUALIFIER]);
            }
            else {
                $q->andFilterWhere([Round::tableName() . '.id' => $this->round]);
            }
        }

        /** five sets filter */
        $q->andWhere(['five_sets' => $this->five_sets]);

        return $q;
    }

}