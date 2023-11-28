<?php

namespace frontend\models\sport;

use frontend\models\sport\query\EventQuery;
use frontend\models\sport\query\TotalQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sp_total".
 *
 * @property int $id
 * @property int|null $tour_id
 * @property int|null $surface_id
 * @property int|null $event_id
 * @property string|null $type
 * @property int|null $profit_1
 * @property int|null $profit_2
 * @property int|null $profit_3
 * @property int|null $profit_4
 * @property int|null $profit_5
 *
 * @property Event $event
 * @property Surface $surface
 * @property Tour $tour
 */
class Total extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_total';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['tour_id', 'surface_id', 'event_id', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'profit_5'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['surface_id'], 'exist', 'skipOnError' => true, 'targetClass' => Surface::class, 'targetAttribute' => ['surface_id' => 'id']],
            [['tour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tour::class, 'targetAttribute' => ['tour_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'tour_id' => 'Tour ID',
            'surface_id' => 'Surface ID',
            'event_id' => 'Event ID',
            'type' => 'Type',
            'profit_1' => 'Profit 1',
            'profit_2' => 'Profit 2',
            'profit_3' => 'Profit 3',
            'profit_4' => 'Profit 4',
            'profit_5' => 'Profit 5',
        ];
    }

    /**
     * Gets query for [[Event]].
     *
     * @return ActiveQuery
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Gets query for [[Surface]].
     *
     * @return ActiveQuery
     */
    public function getSurface(): ActiveQuery
    {
        return $this->hasOne(Surface::class, ['id' => 'surface_id']);
    }

    /**
     * Gets query for [[Tour]].
     *
     * @return ActiveQuery
     */
    public function getTour(): ActiveQuery
    {
        return $this->hasOne(Tour::class, ['id' => 'tour_id']);
    }

    /**
     * {@inheritdoc}
     * @return TotalQuery the active query used by this AR class.
     */
    public static function find(): TotalQuery
    {
        return new TotalQuery(get_called_class());
    }
}
