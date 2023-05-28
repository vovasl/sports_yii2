<?php


namespace backend\models;

use frontend\models\sport\Event;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AddResultForm extends Model
{
    public $id;
    public $result;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'result'], 'required'],
            ['result', 'validateResult'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'Event',
            'result' => 'Result'
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateResult($attribute, $params)
    {
        $pattern = '#(.+)\((.+?)\)#';
        preg_match_all($pattern, $this->{$attribute}, $matches);

        if(!is_array($matches) || empty($matches[1]) || empty($matches[2])) {
            $this->addError($attribute, 'Wrong result format');
        }
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return ArrayHelper::map(Event::find()
            ->withData()
            ->where(['or',
                ['home_result' => NULL],
                ['away_result' => NULL]
            ])
            //->andWhere(['tournament' => 18])
            ->all()
        , 'id', 'fullInfo');
    }
}