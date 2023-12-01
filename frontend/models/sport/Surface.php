<?php

namespace frontend\models\sport;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_surface".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tournament[] $tournaments
 */
class Surface extends ActiveRecord
{

    CONST HARD_INDOOR = [2, 4];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_surface';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[tournaments]].
     *
     * @return ActiveQuery
     */
    public function getTournaments(): ActiveQuery
    {
        return $this->hasMany(Tournament::class, ['surface' => 'id']);
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

}
