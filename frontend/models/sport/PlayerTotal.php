<?php

namespace frontend\models\sport;

use backend\models\statistic\total\PlayerTotalSearch;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tn_player_total".
 *
 * @property int $id
 * @property int $player_id
 * @property int $tour_id
 * @property int $surface_id
 * @property string $type
 * @property int $favorite
 *
 * @property Player $player
 * @property Surface $surface
 * @property Tour $tour
 */
class PlayerTotal extends ActiveRecord
{

    CONST ACTION = [
        'add' => 'add',
        'remove' => 'remove'
    ];

    CONST TYPE = [
        'over-favorite' => 'over-favorite'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player_total';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'tour_id', 'surface_id', 'type'], 'required'],
            [['player_id', 'tour_id', 'surface_id', 'favorite'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
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
            'player_id' => 'Player ID',
            'tour_id' => 'Tour ID',
            'surface_id' => 'Surface ID',
            'type' => 'Type',
            'favorite' => 'Favorite'
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return ActiveQuery
     */
    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
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
     * @param Statistic $model
     * @param PlayerTotalSearch $searchModel
     * @return array
     */
    public static function getPlayersSearchData(Statistic $model, PlayerTotalSearch $searchModel): array
    {
        return [
            'player_id' => $model->player_id,
            'tour_id' => $searchModel->tour,
            'surface_id' => $searchModel->surface,
            'type' => (strpos($searchModel->min_moneyline, '<') !== false && $searchModel->add_type == Odd::ADD_TYPE['over'])
                ? PlayerTotal::TYPE['over-favorite']
                : $searchModel->add_type,
            'favorite' => strpos($searchModel->min_moneyline, '<') !== false ? 1 : 0
        ];
    }
}
