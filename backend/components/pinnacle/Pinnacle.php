<?php

namespace backend\components\pinnacle;


use yii\base\Component;
use backend\components\pinnacle\models\Setting;
use backend\components\pinnacle\models\League;
use backend\components\pinnacle\models\Fixture;
use backend\components\pinnacle\helpers\BaseHelper;

class Pinnacle extends Component
{

    const TENNIS = 33;
    const TENNIS_ODDS_CONFIG = [
        'sets' => ['moneyline', 'spreads', 'totals'],
        'games' => ['spreads', 'totals', 'teamTotal'],
    ];
    const ATP = ['ATP Challenger', 'ATP', 'Davis Cup'];

    /**
     * @param array $settings
     * @return array
     */
    public function run(array $settings): array
    {
        /** get leagues */
        $leagues = $this->getLeagues($settings);

        /** get fixtures with odds */
        $fixtures = [];
        foreach ($leagues as $league) {
            $fixtures = array_merge($fixtures, $this->getFixtures($league));
        }

        return $fixtures;
    }

    /**
     * Get leagues
     * @param array $settings
     * @return array
     */
    public function getLeagues(array $settings): array
    {
        /** get league config */
        $config = Setting::getSettings();
        if(isset($settings['tour']) && is_array($settings['tour'])) $settings['tour'] = implode('|', Pinnacle::ATP);
        $config['base'] = $settings;

        /** get leagues */
        $league = new League($config);
        return $league->getLeagues();
    }

    /**
     * Get fixtures with odds
     * @param array $settings
     * @return array
     */
    public function getFixtures(array $settings): array
    {
        /** get fixture config */
        $config = Setting::getSettings();
        $config['fixture'] = $settings;

        /** get events */
        $fixture = new Fixture($config);
        return $fixture->getFixtures();
    }
}