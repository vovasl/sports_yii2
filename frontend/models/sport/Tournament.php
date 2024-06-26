<?php

namespace frontend\models\sport;


use frontend\models\sport\query\TournamentQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_tournament".
 *
 * @property int $id
 * @property int|null $tour
 * @property string $name
 * @property int|null $surface
 * @property string|null $comment
 * @property int $count_events
 * @property int $sofa_id
 *
 * @property Surface $tournamentSurface
 * @property Event[] $events
 * @property Tour $tournamentTour
 */
class Tournament extends ActiveRecord
{

    public $count_events;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_tournament';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['tour', 'surface', 'count_events', 'sofa_id'], 'integer'],
            [['name'], 'required'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['surface'], 'exist', 'skipOnError' => true, 'targetClass' => Surface::class, 'targetAttribute' => ['surface' => 'id']],
            [['tour'], 'exist', 'skipOnError' => true, 'targetClass' => Tour::class, 'targetAttribute' => ['tour' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'tour' => 'Tour',
            'name' => 'Tournament',
            'surface' => 'Surface',
            'comment' => 'Comment',
            'sofa_id' => 'Sofascore ID',
        ];
    }

    /**
     * Gets query for [[tournamentSurface]].
     *
     * @return ActiveQuery
     */
    public function getTournamentSurface(): ActiveQuery
    {
        return $this->hasOne(Surface::class, ['id' => 'surface']);
    }

    /**
     * Gets query for [[events]].
     *
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['tournament' => 'id']);
    }

    /**
     * Gets query for [[tournamentTour]].
     *
     * @return ActiveQuery
     */
    public function getTournamentTour(): ActiveQuery
    {
        return $this->hasOne(Tour::class, ['id' => 'tour']);
    }

    /**
     * {@inheritdoc}
     * @return TournamentQuery the active query used by this AR class.
     */
    public static function find(): TournamentQuery
    {
        return new TournamentQuery(get_called_class());
    }

    /**
     * @param $value
     * @return Tournament|null
     */
    public static function findBySofa($value): ?Tournament
    {
        return self::findOne(['sofa_id' => $value]);
    }

    /**
     * @param $value
     * @return int|null
     */
    public static function getIdBySofa($value): ?int
    {
        return ($res = self::findBySofa($value)) ? $res->id : null;
    }

}
