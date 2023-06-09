<?php

namespace backend\components\pinnacle\models;


use Yii;

/**
 * This is the model class for table "pn_settings".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 */
class Setting extends \yii\db\ActiveRecord
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
