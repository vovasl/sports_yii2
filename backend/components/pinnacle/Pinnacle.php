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

    /**
     * @var Client
     */
    private $client;


    public function run()
    {

        $this->settings = Setting::getSettings();
        $this->settings['fixture'] = [
            'sportid' => 33,
            'tour' => 'ATP',
        ];
        $this->client = new Client($this->settings['username'],$this->settings['pass']);

        $leagues = $this->getLeagues();

        print_r($leagues);

        /*
        $settings2 = [
            'sportid' => 33,
            'tour' => 'ATP',
        ];

        $this->settings = Setting::getSettings();
        $this->settings['fixture'] = $settings2;


        $leagues = $this->getLeagues();

        foreach ($leagues as $league) {
            $this->settings['fixture'] = $settings;
            $fixtures = $this->getFixtures();
            //var_dump($fixtures);
        }
        */
    }

    /**
     * Get leagues
     * @return array
     */
    public function getLeagues(): array
    {
        $league = new League($this->client, $this->settings);
        return $league->getLeagues();
    }

    /**
     * Get events with odds
     */
    public function getFixtures(): array
    {
        /** get events */
        $fixture = new Fixture($this->client, $this->settings);
        return $fixture->getFixtures();
    }
}