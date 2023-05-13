<?php

namespace app\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sp_odd".
 *
 * @property int $id
 * @property int $event
 * @property int|null $type
 * @property string $value
 * @property string $odd
 * @property string|null $created_at
 *
 * @property Event $eventOdd
 * @property OddType $oddType
 */
class Odd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_odd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['event', 'value', 'odd'], 'required'],
            [['event', 'type'], 'integer'],
            [['created_at'], 'safe'],
            [['value', 'odd'], 'string', 'max' => 255],
            [['event'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => OddType::class, 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'type' => 'Type',
            'value' => 'Value',
            'odd' => 'Odd',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[eventOdd]].
     *
     * @return ActiveQuery
     */
    public function getEventOdd(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event']);
    }

    /**
     * Gets query for [[oddType]].
     *
     * @return ActiveQuery
     */
    public function getOddType(): ActiveQuery
    {
        return $this->hasOne(OddType::class, ['id' => 'type']);
    }
}
