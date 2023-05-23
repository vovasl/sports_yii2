<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sp_event_log".
 *
 * @property int $id
 * @property string $created_at
 * @property int $event_id
 * @property string|null $message
 *
 * @property Event $event
 */
class EventLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_event_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['event_id'], 'required'],
            [['event_id'], 'integer'],
            [['message'], 'string'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'event_id' => 'Event ID',
            'message' => 'Message',
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
}
