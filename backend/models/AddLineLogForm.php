<?php

namespace backend\models;


use yii\base\Model;

class AddLineLogForm extends Model
{

    public $save;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['save'], 'required'],
            [['save'], 'integer'],
        ];
    }

}