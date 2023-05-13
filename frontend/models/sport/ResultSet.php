<?php

namespace app\models\sport;

use app\models\sport\query\EventQuery;
use app\models\sport\query\ResultSetQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_result_set".
 *
 * @property int $id
 * @property int $event
 * @property int $set
 * @property int|null $home
 * @property int|null $away
 *
 * @property Event $eventResultSet
 */
class ResultSet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_result_set';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event', 'set'], 'required'],
            [['event', 'set', 'home', 'away'], 'integer'],
            [['event'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event' => 'id']],
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
            'set' => 'Set',
            'home' => 'Home',
            'away' => 'Away',
        ];
    }

    /**
     * Gets query for [[eventResultSet]].
     *
     * @return ActiveQuery
     */
    public function getEventResultSet(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event']);
    }

    /**
     * {@inheritdoc}
     * @return ResultSetQuery the active query used by this AR class.
     */
    public static function find(): ResultSetQuery
    {
        return new ResultSetQuery(get_called_class());
    }
}
