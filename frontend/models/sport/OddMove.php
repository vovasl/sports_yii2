<?php

namespace frontend\models\sport;


use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "sp_odd_move".
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $type_id
 * @property string|null $add_type
 * @property string|null $value
 * @property int $status
 *
 * @property Event $event
 * @property OddType $type
 */
class OddMove extends ActiveRecord
{

    CONST POINTS = 10;

    CONST STATUSES = [
        'finished' => 0,
        'open' => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'sp_odd_move';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['event_id'], 'required'],
            [['event_id', 'type_id', 'status', 'value'], 'integer'],
            [['add_type'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => OddType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'type_id' => 'Type ID',
            'add_type' => 'Add Type',
            'value' => 'Value',
            'status' => 'Status',
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
     * Gets query for [[Type]].
     *
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(OddType::class, ['id' => 'type_id']);
    }

    /**
     * @return array
     */
    public static function dropdownFilterStatuses(): array
    {
        return array_map('ucfirst', array_flip(self::STATUSES));
    }

    /**
     * @param Event $event
     * @param int $status
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function addEvent(Event $event, int $status = self::STATUSES['open']): bool
    {
        /** get favorite - base odd model */
        $favorite = $event->homeMoneyline[0]->odd < $event->awayMoneyline[0]->odd
            ? 'homeMoneyline'
            : 'awayMoneyline'
        ;
        $oddModel = $event->{$favorite}[0];

        /** get last odd */
        $oddHistory = OddHistory::find()
            ->select('odd')
            ->where([
                'event' => $event->id,
                'player_id' => $oddModel->player_id
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one()
        ;

        if(!$oddHistory) return true;

        /** get model */
        $model = ($model = OddMove::findOne(['event_id' => $event->id])) ? $model : new OddMove();

        /** min up and min down odds */
        $minUp = $oddModel->odd + OddMove::POINTS;
        $minDown = $oddModel->odd - OddMove::POINTS;

        /** odds not change */
        if($oddHistory->odd < $minUp && $oddHistory->odd > $minDown) {
            $model->delete();
            return true;
        }

        /** save */
        if ($model->isNewRecord) {
            $model->event_id = $event->id;
            $model->type_id = $oddModel->type;
        }
        $model->value = abs($oddModel->odd - $oddHistory->odd);
        $model->status = $status;
        $model->save();

        return true;
    }
}
