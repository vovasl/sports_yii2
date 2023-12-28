<?php

namespace backend\components\ps3838;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\ps3838\models\Fixture;
use backend\models\Setting;
use backend\components\ps3838\models\League;
use yii\base\Component;

class PS3838 extends Component
{

    const TENNIS = 33;
    const TENNIS_ODDS_CONFIG = [
        'sets' => [
            'moneyline',
            'spreads',
            'totals'
        ],
        'games' => [
            'spreads',
            'totals',
            'teamTotal'
        ],
    ];

    const TOUR = [
        'ATP Challenger',
        'ATP',
        'Davis Cup',
        'United Cup Men',
        'United Cup Womens',
        //'ITF Men',
        //'ITF Women',
        'Fed Cup',
        'WTA',
    ];

    /**
     * @param array $settings
     * @return array
     */
    public function run(array $settings): array
    {
        //return [];

        /** get leagues */
        $leagues = $this->getLeagues($settings);
        if(count($leagues) == 0) {
            // ::log no leagues
            return [];
        }
        $leaguesSettings = [
            'sportId' => $settings['sportId'],
            'leagueIds' => implode(',', $this->getLeagues($settings))
        ];

        /** get events with odds */
        $events = $this->getFixtures($leaguesSettings);

        return $events;
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
        if(isset($settings['tour']) && is_array($settings['tour'])) {
            $settings['tour'] = implode('|', self::TOUR);
        }
        $config['base'] = $settings;

        /** get leagues */
        $league = new League($config);
        return $league->getLeagues();
    }

    /**
     * Get events with odds
     * @param array $leagues
     * @return array
     */
    public function getFixtures(array $leagues): array
    {
        /** get fixture config */
        $config = Setting::getSettings();
        $config['fixture'] = $leagues;

        /** get events */
        $fixture = new Fixture($config);
        $events = $fixture->getFixtures();

        return $events;
    }
}