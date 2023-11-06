<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_tour".
 *
 * @property int $id
 * @property string $name
 *
 * @property Tournament[] $tournaments
 */
class Tour extends \yii\db\ActiveRecord
{

    const ATP = 1;
    const CHALLENGER = 2;
    const SOFA_CHALLENGER = 72;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_tour';
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
        return $this->hasMany(Tournament::class, ['tour' => 'id']);
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

}
