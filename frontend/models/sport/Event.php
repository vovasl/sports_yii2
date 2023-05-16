<?php

namespace frontend\models\sport;

use frontend\models\sport\query\EventQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tn_event".
 *
 * @property int $id
 * @property string|null $start_at
 * @property int|null $tournament
 * @property int|null $round
 * @property int|null $home
 * @property int|null $away
 * @property int|null $home_result
 * @property int|null $away_result
 * @property int|null $winner
 * @property int|null $total
 * @property int $status
 * @property int|null $total_games
 * @property int $five_sets
 * @property int $pin_id
 *
 * @property Player $playerHome
 * @property Player $playerAway
 * @property Player $playerWinner
 * @property Round $tournamentRound
 * @property Odd[] $odds
 * @property ResultSet[] $resultSets
 * @property Tournament $eventTournament
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['start_at'], 'safe'],
            [['tournament', 'round', 'home', 'away', 'home_result', 'away_result', 'winner', 'total', 'status', 'total_games', 'five_sets', 'pin_id'], 'integer'],
            [['away'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['away' => 'id']],
            [['home'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['home' => 'id']],
            [['round'], 'exist', 'skipOnError' => true, 'targetClass' => Round::class, 'targetAttribute' => ['round' => 'id']],
            [['tournament'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::class, 'targetAttribute' => ['tournament' => 'id']],
            [['winner'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['winner' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'start_at' => 'Start At',
            'tournament' => 'Tournament',
            'round' => 'Round',
            'home' => 'Home',
            'away' => 'Away',
            'home_result' => 'Home Result',
            'away_result' => 'Away Result',
            'winner' => 'Winner',
            'total' => 'Total',
            'status' => 'Status',
            'total_games' => 'Total Games',
            'five_sets' => 'Five Sets',
            'pin_id' => 'Pinnacle ID'
        ];
    }

    /**
     * Gets query for [[playerHome]].
     *
     * @return ActiveQuery
     */
    public function getPlayerHome(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'home']);
    }

    /**
     * Gets query for [[playerAway]].
     *
     * @return ActiveQuery
     */
    public function getPlayerAway(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'away']);
    }

    /**
     * Gets query for [[playerWinner]].
     *
     * @return ActiveQuery
     */
    public function getPlayerWinner(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'winner']);
    }

    /**
     * Gets query for [[tournamentRound]].
     *
     * @return ActiveQuery
     */
    public function getTournamentRound(): ActiveQuery
    {
        return $this->hasOne(Round::class, ['id' => 'round']);
    }

    /**
     * Gets query for [[odds]].
     *
     * @return ActiveQuery
     */
    public function getOdds(): ActiveQuery
    {
        return $this->hasMany(Odd::class, ['event' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeMoneyline(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['player_id' => 'id'])
            ->via('playerHome')
            ->leftJoin('sp_odd_type', 'sp_odd_type.id = sp_odd.type')
            ->where(['sp_odd_type.name' => OddType::MONEYLINE])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwayMoneyline(): ActiveQuery
    {
        return $this->hasOne(Odd::class, ['player_id' => 'id'])
            ->via('playerAway')
            ->leftJoin('sp_odd_type', 'sp_odd_type.id = sp_odd.type')
            ->where(['sp_odd_type.name' => OddType::MONEYLINE])
            ;
    }

    /**
     * Gets query for [[resultSets]].
     *
     * @return ActiveQuery
     */
    public function getResultSets(): ActiveQuery
    {
        return $this->hasMany(ResultSet::class, ['event' => 'id']);
    }

    /**
     * Gets query for [[eventTournament]].
     *
     * @return ActiveQuery
     */
    public function getEventTournament(): ActiveQuery
    {
        return $this->hasOne(Tournament::class, ['id' => 'tournament']);
    }

    /**
     * {@inheritdoc}
     * @return EventQuery the active query used by this AR class.
     */
    public static function find(): EventQuery
    {
        return new EventQuery(get_called_class());
    }
}
