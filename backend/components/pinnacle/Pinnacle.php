<?php

namespace backend\components\pinnacle;


use Yii;
use yii\base\Component;
use backend\components\pinnacle\models\Setting;
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
            //BaseHelper::outputArray($fixtures);

        }
    }

    /**
     * Get leagues
     * @return array
     */
    public function getLeagues(): array
    {
        /** get leagues */
        $league = new League($this->settings);
        return $league->getLeagues();
    }

    /**
     * Get events with odds
     */
    public function getFixtures(): array
    {
        /** get events */
        $fixture = new Fixture($this->settings);
        return $fixture->getFixtures();
    }
}