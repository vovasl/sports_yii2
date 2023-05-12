<?php

namespace backend\components\pinnacle;


use Yii;
use yii\base\Component;
use backend\components\pinnacle\models\Setting;
use backend\components\pinnacle\services\Client;
use backend\components\pinnacle\models\League;
use backend\components\pinnacle\models\Fixture;
use backend\components\pinnacle\helpers\BaseHelper;

class Pinnacle extends Component
{

    /**
     * @var array
     */
    private $settings;

    const TENNIS = 33;
    const TENNIS_CONFIG = [
        'sets' => ['moneyline', 'spreads', 'totals'],
        'games' => ['spreads', 'totals', 'teamTotal'],
    ];
    const ATP = 'ATP';
    const WTA = 'WTA';

    public function run()
    {

        $this->settings = Setting::getSettings();
        $this->settings['fixture'] = [
            'sportid' => self::TENNIS,
            'tour' => self::ATP,
        ];
        $leagues = $this->getLeagues();

        foreach ($leagues as $league) {
            $this->settings['fixture'] = $league;
            $fixtures = $this->getFixtures();
            echo BaseHelper::events($fixtures, 'tennis');
        }
    }

    /**
     * Get leagues
     * @return array
     */
    public function getLeagues(): array
    {
        $client = new Client($this->settings['username'],$this->settings['pass']);

        $league = new League($client, $this->settings);
        return $league->getLeagues();
    }

    /**
     * Get events with odds
     */
    public function getFixtures(): array
    {
        $client = new Client($this->settings['username'],$this->settings['pass']);

        /** get events */
        $fixture = new Fixture($client, $this->settings);
        return $fixture->getFixtures();
    }
}