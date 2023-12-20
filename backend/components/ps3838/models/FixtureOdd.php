<?php

namespace backend\components\ps3838\models;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\ps3838\PS3838;

class FixtureOdd
{

    /**
     * @var array
     */
    private $settings;

    /**
     * FixtureOdd constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param $fixtures
     * @param $odds
     * @return array
     */
    public function relate($fixtures, $odds): array
    {
        foreach ($odds as $key => $odd) {
            if(isset($fixtures[$key])) {
                $fixture = $fixtures[$key];
                $oddsKey = strtolower(trim($fixture['resultingUnit']));

                // child fixture
                if(isset($fixture['parentId'])) {
                    $fixture = $fixtures[$fixture['parentId']];
                }

                $fixture['odds'][$oddsKey] = $odd;
                $fixtures[$fixture['id']] = $fixture;
            }
        }

        /** fixtures with odds */
        $fixtures = array_filter($fixtures, function ($fixture) {
            return isset($fixture['odds']);
        });


        /** prepare fixture odds */
        $fixtures = $this->prepareOdds($fixtures);

        return $fixtures;
    }

    /**
     * @param $fixtures
     * @return array|mixed
     */
    private function prepareOdds($fixtures)
    {
        switch ($this->settings['fixture']['sportId']) {
            case PS3838::TENNIS:
                return $this->prepareTennisOdds($fixtures);
            default:
                return $fixtures;
        }
    }

    /**
     * @param $fixtures
     * @return array
     */
    private function prepareTennisOdds($fixtures): array
    {
        foreach ($fixtures as $id => $fixture) {
            foreach($fixture['odds'] as $type => $odds) {
                $fixture['odds'][$type] = $this->prepareTennisLine($type, $odds[0]);
            }
            $fixtures[$id] = $fixture;
        }
        return $fixtures;
    }

    /**
     * @param $type
     * @param $odd
     * @return array
     */
    private function prepareTennisLine($type, $odd): array
    {
        $odd = $this->removeTennisLineFields($type, $odd);
        return $odd;
    }

    /**
     * @param $type
     * @param $odd
     * @return array
     */
    private function removeTennisLineFields($type, $odd): array
    {
        $fields = ['altLineId', 'max'];
        foreach (PS3838::TENNIS_ODDS_CONFIG[$type] as $line) {
            if(!isset($odd[$line])) continue;
            foreach($odd[$line] as $k => $lineOdd) {
                foreach ($fields as $remove) {
                    unset($lineOdd[$remove]);
                }
                $odd[$line][$k] = $lineOdd;
            }
        }

        return $odd;
    }
}