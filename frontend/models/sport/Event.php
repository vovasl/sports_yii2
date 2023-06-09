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
 * @property ResultSet[] $resultSets
 * @property Tournament $eventTournament
 * @property Odd[] $odds
 * @property Odd[] $homeOdds
 * @property Odd[] $awayOdds
 * @property Odd[] $homeMoneyline
 * @property Odd[] $awayMoneyline
 * @property Odd[] $totalsUnder
 * @property Odd[] $totalsOver
 * @property Odd[] $setsTotalsUnder
 * @property Odd[] $setsTotalsOver
 * @property Odd[] $homeTotalsUnder
 * @property Odd[] $awayTotalsUnder
 * @property Odd[] $homeTotalsOver
 * @property Odd[] $awayTotalsOver
 * @property Odd[] $homeSpreads
 * @property Odd[] $awaySpreads
 * @property Odd[] $homeSetsSpreads
 * @property Odd[] $awaySetsSpreads
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
     * Gets query for [[odds]].
     *
     * @return ActiveQuery
     */
    public function getOdds(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, ['event' => 'id'])
            ->joinWith('oddType', false)
            ->orderBy([
                'sp_odd.value' => SORT_ASC
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeOdds(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, ['event' => 'id', 'player_id' => 'home'])
            ->joinWith('oddType', false)
            ->orderBy([
                'sp_odd.value' => SORT_ASC
            ])
        ;

    }

    /**
     * @return ActiveQuery
     */
    public function getAwayOdds(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, ['event' => 'id', 'player_id' => 'away'])
            ->joinWith('oddType', false)
            ->orderBy([
                'sp_odd.value' => SORT_ASC
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeMoneyline(): ActiveQuery
    {
        return $this
            ->getHomeOdds()
            ->where([
                'sp_odd_type.name' => OddType::MONEYLINE
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwayMoneyline(): ActiveQuery
    {
        return $this
            ->getAwayOdds()
            ->where([
                'sp_odd_type.name' => OddType::MONEYLINE
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getTotalsUnder(): ActiveQuery
    {
        return $this
            ->getOdds()
            ->where([
                'sp_odd_type.name' => OddType::TOTALS,
                'sp_odd.add_type' => Odd::ADD_TYPE['under']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getTotalsOver(): ActiveQuery
    {
        return $this
            ->getOdds()
            ->where([
                'sp_odd_type.name' => OddType::TOTALS,
                'sp_odd.add_type' => Odd::ADD_TYPE['over']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getSetsTotalsUnder(): ActiveQuery
    {
        return $this
            ->getOdds()
            ->where([
                'sp_odd_type.name' => OddType::SETS_TOTALS,
                'sp_odd.add_type' => Odd::ADD_TYPE['under']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getSetsTotalsOver(): ActiveQuery
    {
        return $this
            ->getOdds()
            ->where([
                'sp_odd_type.name' => OddType::SETS_TOTALS,
                'sp_odd.add_type' => Odd::ADD_TYPE['over']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeTotalsUnder(): ActiveQuery
    {
        return $this
            ->getHomeOdds()
            ->where([
                'sp_odd_type.name' => OddType::TEAM_TOTAL,
                'sp_odd.add_type' => Odd::ADD_TYPE['under']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwayTotalsUnder(): ActiveQuery
    {
        return $this
            ->getAwayOdds()
            ->where([
                'sp_odd_type.name' => OddType::TEAM_TOTAL,
                'sp_odd.add_type' => Odd::ADD_TYPE['under']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeTotalsOver(): ActiveQuery
    {
        return $this
            ->getHomeOdds()
            ->where([
                'sp_odd_type.name' => OddType::TEAM_TOTAL,
                'sp_odd.add_type' => Odd::ADD_TYPE['over']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwayTotalsOver(): ActiveQuery
    {
        return $this
            ->getAwayOdds()
            ->where([
                'sp_odd_type.name' => OddType::TEAM_TOTAL,
                'sp_odd.add_type' => Odd::ADD_TYPE['over']
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeSpreads(): ActiveQuery
    {
        return $this
            ->getHomeOdds()
            ->where([
                'sp_odd_type.name' => OddType::SPREADS,
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwaySpreads(): ActiveQuery
    {
        return $this
            ->getAwayOdds()
            ->where([
                'sp_odd_type.name' => OddType::SPREADS,
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeSetsSpreads(): ActiveQuery
    {
        return $this
            ->getHomeOdds()
            ->where([
                'sp_odd_type.name' => OddType::SETS_SPREADS,
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwaySetsSpreads(): ActiveQuery
    {
        return $this
            ->getAwayOdds()
            ->where([
                'sp_odd_type.name' => OddType::SETS_SPREADS,
            ])
        ;
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
