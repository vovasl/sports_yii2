<?php

namespace backend\models;


use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pn_settings".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 */
class Setting extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pn_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        $settings = self::find()->select([ 'value', 'name'])->indexBy('name')->column();

        //prepare settings

        return $settings;
    }
}
