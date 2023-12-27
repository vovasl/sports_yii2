<?php

namespace frontend\models\sport;


use frontend\models\sport\query\EventQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
 * @property int $sofa_id
 * @property string|null $created
 * @property int $o_id
 * @property string $o_add_type
 * @property int $o_odd
 * @property int $o_profit
 * @property int $o_value
 *
 * @property Player $homePlayer
 * @property Player $awayPlayer
 * @property Player $playerWinner
 * @property Round $tournamentRound
 * @property ResultSet[] $setsResult
 * @property Tournament $eventTournament
 * @property Odd[] $odds
 * @property Odd[] $oddsHistory
 * @property Odd[] $oddsMove
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
class Event extends ActiveRecord
{

    public $o_id;
    public $o_add_type;
    public $o_profit;
    public $o_odd;
    public $o_value;
    public $o_type_name;
    public $count_odds;
    public $home_moneyline_odd;
    public $away_moneyline_odd;
    public $total_over_value;
    public $odd_move_value;
    public $odd_move_value_type;
    public $odd_move_status;

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }


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
            [['tournament', 'round', 'home', 'away', 'home_result', 'away_result', 'winner', 'total', 'status', 'total_games', 'five_sets', 'pin_id', 'sofa_id', 'o_id', 'o_odd', 'o_profit', 'home_moneyline_odd', 'away_moneyline_odd'], 'integer'],
            [['o_add_type', 'o_value', 'count_odds', 'total_over_value'], 'string'],
            [['start_at', 'created'], 'safe'],
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
            'pin_id' => 'Pinnacle ID',
            'sofa_id' => 'Sofascore ID',
            'created' => 'Created',
        ];
    }

    /**
     * {@inheritdoc}
     * @return EventQuery the active query used by this AR class.
     */
    public static function find(): EventQuery
    {
        return new EventQuery(get_called_class());
    }

    /**
     * Gets query for [[homePlayer]].
     *
     * @return ActiveQuery
     */
    public function getHomePlayer(): ActiveQuery
    {
        return $this->hasOne(Player::class, ['id' => 'home']);
    }

    /**
     * Gets query for [[awayPlayer]].
     *
     * @return ActiveQuery
     */
    public function getAwayPlayer(): ActiveQuery
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
     * Gets query for [[setsResult]].
     *
     * @return ActiveQuery
     */
    public function getSetsResult(): ActiveQuery
    {
        return $this->hasMany(ResultSet::class, ['event' => 'id'])
            ->orderBy([
                'set' => SORT_ASC
            ])
        ;
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
    public function getOddsHistory(): ActiveQuery
    {
        return $this
            ->hasMany(OddHistory::class, ['event' => 'id'])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getOddsMove(): ActiveQuery
    {
        return $this
            ->hasMany(OddMove::class, ['event_id' => 'id'])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getHomeMoneyline(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, [
                'event' => 'id',
                'player_id' =>  'home',
            ])
            ->from(['home_moneyline' => Odd::tableName()])
            ->onCondition([
                'home_moneyline.type' => Odd::TYPE['moneyline'],
            ])
            ->orderBy([
                'home_moneyline.value' => SORT_ASC
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getAwayMoneyline(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, [
                'event' => 'id',
                'player_id' =>  'away',
            ])
            ->from(['away_moneyline' => Odd::tableName()])
            ->onCondition([
                'away_moneyline.type' => Odd::TYPE['moneyline'],
            ])
            ->orderBy([
                'away_moneyline.value' => SORT_ASC
            ])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getTotalsOver(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, [
                'event' => 'id',
            ])
            ->from(['total_over' => Odd::tableName()])
            ->onCondition([
                'total_over.type' => Odd::TYPE['totals'],
                'total_over.add_type' => Odd::ADD_TYPE['over']
            ])
            ->orderBy([
                'total_over.value' => SORT_ASC
            ])
            ;
    }

    /**
     * @return ActiveQuery
     */
    public function getTotalsUnder(): ActiveQuery
    {
        return $this
            ->hasMany(Odd::class, [
                'event' => 'id',
            ])
            ->from(['total_under' => Odd::tableName()])
            ->onCondition([
                'total_under.type' => Odd::TYPE['totals'],
                'total_under.add_type' => Odd::ADD_TYPE['under']
            ])
            ->orderBy([
                'total_under.value' => SORT_DESC
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
            //->orderBy(['sp_odd.value' => SORT_ASC])
            ->orderBy(new Expression('sp_odd.value * 1 DESC'))
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
            //->orderBy(['sp_odd.value' => SORT_ASC])
            ->orderBy(new Expression('sp_odd.value * 1 ASC'))
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
     * @return string
     */
    public function getFormatStartAt(): string
    {
        return date('d.m H:i', strtotime($this->start_at));
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->homePlayer->name} - {$this->awayPlayer->name}";
    }

    /**
     * @return string
     */
    public function getFullNameLink(): string
    {
        return "{$this->outputPlayer('homePlayer')} - {$this->outputPlayer('awayPlayer')}";
    }

    /**
     * @return string
     */
    public function getFullInfo(): string
    {
        return "{$this->id}. {$this->formatStartAt} {$this->eventTournament->name} {$this->tournamentRound->name} {$this->fullName}";
    }

    /**
     * @return string
     */
    public function getMoneyline(): string
    {
        if(empty($this->homeMoneyline[0]->oddVal) || empty($this->awayMoneyline[0]->oddVal)) return '';
        return Odd::getValueProfit($this->homeMoneyline[0]->profit, $this->homeMoneyline[0]->oddVal)
               . ' - '
               . Odd::getValueProfit($this->awayMoneyline[0]->profit, $this->awayMoneyline[0]->oddVal)
        ;
    }


    /**
     * @param $methods
     * @return array
     */
    public function getTotals($methods): array
    {
        $data = [];
        foreach ($methods as $k => $method) {
            if(empty($this->{$method})) return [];
            foreach ($this->{$method} as $odd) {
                //$data[$odd->value][$k] = $odd->oddVal;
                $data[$odd->value][$k] = Odd::getValueProfit($odd->profit, $odd->oddVal);
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        if(is_null($this->home_result) || is_null($this->away_result)) return '';
        if(count($this->setsResult) == 0) return '';

        $games = [];
        foreach ($this->setsResult as $result) {
            $games[] = "{$result->home}:{$result->away}";
        }
        $sets = implode(', ', $games);
        return "{$this->home_result}:{$this->away_result}({$sets})";
    }

    /**
     * @param $type
     * @return string
     */
    public function getStatsTotal($type): string
    {
        if(is_null($this->winner)) return '';

        $i = 0;
        foreach($this->{$type} as $odd) {
            if($odd->profit > 0) $i++;
        }
        return "{$i}/" . count($this->{$type});
    }

    /**
     * @return array
     */
    public function dropdownPlayers(): array
    {
        return [
            $this->home => $this->homePlayer->name,
            $this->away => $this->awayPlayer->name
        ];
    }

    /**
     * @param $field
     * @param array $uri
     * @return string
     */
    public function outputPlayer($field, array $uri = []): string
    {
        $player = $this->{$field};
        $class = ($this->winner === $player->id) ? 'winner' : '';
        return Player::getEventsLink($player->name, $uri, $class);
    }

    /**
     * @return bool
     */
    public function actionUpdate(): bool
    {
        return ($this->pin_id === null || $this->sofa_id === null);
    }

    /**
     * @return bool
     */
    public function actionDelete(): bool
    {
        return ($this->winner === null && strtotime($this->start_at) < time() - 60 * 60 * 3);
    }

    /**
     * @return bool
     */
    public function actionAddLine(): bool
    {
        return ($this->pin_id != null && count($this->odds) == 0);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        /** update event - change odd player */
        if(!$insert) {
            $this->updateOddPlayer($changedAttributes);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param array $oldData
     */
    private function updateOddPlayer(array $oldData)
    {
        $fields = ['home', 'away'];
        foreach ($fields as $field) {
            /** update field */
            if ($oldData[$field] != null && $this->{$field} != $oldData[$field]) {
                Odd::updateAll(
                    ['player_id' => $this->{$field}],
                    ['event' => $this->id, 'player_id' => $oldData[$field]]
                );
            }
        }
    }

    /**
     * @return float
     */
    public function getOddVal(): float
    {
        return $this->o_odd/100;
    }

    /**
     * @return float
     */
    public function getHomeMoneylineOddVal(): float
    {
        return $this->home_moneyline_odd/100;
    }

    /**
     * @return float
     */
    public function getAwayMoneylineOddVal(): float
    {
        return $this->away_moneyline_odd/100;
    }

    /**
     * @return string
     */
    public function getOddMoveValueType(): string
    {
        return ucfirst(array_search($this->odd_move_value_type, OddMove::VALUE_TYPES));
    }

    /**
     * @return string
     */
    public function getOddMoveStatus(): string
    {
        return ucfirst(array_search($this->odd_move_status, OddMove::STATUSES));
    }

}
