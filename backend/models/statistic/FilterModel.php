<?php

namespace backend\models\statistic;


use frontend\models\sport\query\EventQuery;
use frontend\models\sport\Round;
use yii\base\Model;

class FilterModel extends Model
{

    CONST FILTER = [
        'more' => 'more',
        'less' => 'less'
    ];

    public $tour;
    public $surface;
    public $round = [0];
    public $five_sets = 0;
    public $value;

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
            $this->value = $filter['value'];
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
            [['value'], 'string'],
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
            'value' => 'Value',
        ];
    }


    /**
     * @param EventQuery $q
     * @return EventQuery
     */
    public function searchEvents(EventQuery $q): EventQuery
    {

        $q->andWhere(['IS NOT', 'sp_odd.profit', null]);

        /** tour filter */
        if(!empty($this->tour)) $q->andWhere(['tn_tour.id' => $this->tour]);

        /** surface filter */
        if(!empty($this->surface)) $q->andWhere(['tn_tournament.surface' => $this->surface]);

        /** round filter */
        if(!in_array(0, $this->round)) {
            if(in_array(Round::QUALIFIER_FILTER, $this->round)) {
                $q->andFilterWhere(['NOT IN', 'round', Round::QUALIFIER]);
            }
            else {
                $q->andWhere(['IN', 'round', $this->round]);
            }
        }

        /** five sets filter */
        $q->andWhere(['five_sets' => $this->five_sets]);

        /** value filter */
        if(!empty($this->value)) $q->andWhere(['sp_odd.value' => $this->value]);

        return $q;
    }

}