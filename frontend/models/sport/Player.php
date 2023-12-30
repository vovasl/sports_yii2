<?php

namespace frontend\models\sport;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "tn_player".
 *
 * @property int $id
 * @property int|null $type
 * @property string $name
 * @property string|null $birthday
 * @property string|null $plays
 * @property string|null $comment
 * @property int $sofa_id
 *
 * @property Event[] $homeEvents
 * @property Event[] $awayEvents
 * @property Event[] $winnerEvents
 * @property PlayerType $playerType
 * @property Odd[] $odds;
 */
class Player extends ActiveRecord
{

    public $count_events;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tn_player';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['type', 'sofa_id', 'count_events'], 'integer'],
            [['name'], 'required'],
            [['birthday'], 'safe'],
            [['comment'], 'string'],
            [['name', 'plays'], 'string', 'max' => 255],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => PlayerType::class, 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'birthday' => 'Birthday',
            'plays' => 'Plays',
            'comment' => 'Comment',
            'sofa_id' => 'Sofascore ID',
            'count_events' => 'Events'
        ];
    }

    /**
     * Gets query for [[homeEvents]].
     *
     * @return ActiveQuery
     */
    public function getHomeEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['home' => 'id']);
    }

    /**
     * Gets query for [[awayEvents]].
     *
     * @return ActiveQuery
     */
    public function getAwayEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['away' => 'id']);
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return array_merge($this->homeEvents, $this->awayEvents);
    }

    /**
     * Gets query for [[winnerEvents]].
     *
     * @return ActiveQuery
     */
    public function getWinnerEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['winner' => 'id']);
    }

    /**
     * Gets query for [[playerType]].
     *
     * @return ActiveQuery
     */
    public function getPlayerType(): ActiveQuery
    {
        return $this->hasOne(PlayerType::class, ['id' => 'type']);
    }

    /**
     * Gets query for [[odds]].
     *
     * @return ActiveQuery
     */
    public function getOdds(): ActiveQuery
    {
        return $this->hasMany(Odd::class, ['id' => 'player_id']);
    }

    /**
     * @param $tour
     * @return array
     */
    public function getTourEvents($tour): array
    {
        return array_filter($this->events, function (Event $event) use ($tour) {
            return ($event->eventTournament->tour == $tour);
        });
    }

    /**
     * @param $value
     * @return Player|null
     */
    public static function findBySofa($value): ?Player
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

    /**
     * @param $name
     * @param array $uri
     * @param string $class
     * @return string
     */
    public static function getEventsLink($name, array $uri = [], string $class = ''): string
    {
        $uri['action'] = (isset($uri['action'])) ? $uri['action'] : null;
        $uri['model'] = (isset($uri['model'])) ? $uri['model'] : 'EventSearch';
        $player = (!empty($uri['player_field'])) ? $uri['player_field'] : "{$uri['model']}[player]";
        $link = [
            $uri['action'],
            $player => $name,
        ];
       if(!empty($uri['search_data'])) {
            $link = array_merge($link, $uri['search_data']);
        }
        return Html::a($name, $link, [
            'target'=>'_blank',
            'class' => $class
        ]);
    }

    /**
     * @return array
     */
    public static function dropdown(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->orderBy('name')->column();
    }

    /**
     * @param $name
     * @return array
     */
    public static function dropdownSimilar($name): array
    {
        $player = self::find();
        $player->select(['name', 'id']);

        $search = str_replace('.', '', explode(' ', $name));
        foreach ($search as $val) {
            $player->orWhere(['like', 'name', $val]);
        }

        $player->indexBy('id');
        $player->orderBy('name');
        return $player->column();
    }

    /**
     * @return bool
     */
    public function actionDelete(): bool
    {
        return (count($this->events) < 1);
    }

}
