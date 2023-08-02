<?php

namespace backend\services;


use frontend\models\sport\Player;
use frontend\models\sport\PlayerAdd;
use yii\base\Component;

class CheckPlayer extends Component
{

    /**
     * @return array
     */
    public static function add(): array
    {
        $data = [];
        $playersAdd = PlayerAdd::find()->with('playerAddEvents')->all();
        foreach ($playersAdd as $playerAdd) {
            $q = Player::find();

            /** search for a player by name */
            $name = str_replace('.', '', explode(' ', $playerAdd->name));
            foreach ($name as $val) {
                $q->andWhere(['like', 'name', $val]);
            }

            /** get result data */
            if($player = $q->one()) {
                $data[] = [
                    'player_add' => $playerAdd->name,
                    'player' => $player->name,
                    'player_add_events' => $playerAdd->playerAddEvents
                ];
            }
        }

        return $data;
    }
}