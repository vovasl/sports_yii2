<?php

namespace backend\components\pinnacle;


use Yii;
use yii\base\Component;
use backend\components\pinnacle\models\Setting;
use backend\components\pinnacle\services\Client;
use backend\components\pinnacle\models\League;
use backend\components\pinnacle\models\Fixture;

class Pinnacle extends Component
{

    /**
     * @var array
     */
    private $settings;


    public function run()
    {

        $this->settings = Setting::getSettings();
        $this->settings['fixture'] = [
            'sportid' => 33,
            'tour' => 'ATP',
        ];
        $leagues = $this->getLeagues();

        foreach ($leagues as $league) {
            $this->settings['fixture'] = $league;
            $fixtures = $this->getFixtures();
            echo '<pre>' . print_r($fixtures, 1) . '</pre>';
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