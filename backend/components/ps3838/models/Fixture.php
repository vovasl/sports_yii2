<?php

namespace backend\components\ps3838\models;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\ps3838\PS3838;
use backend\components\ps3838\services\Client;

class Fixture
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $fixtures = [];

    /**
     * @var string
     */
    private $odds;

    /**
     * Fixture constructor.
     * @param array $settings
     * @param int $odds
     */
    public function __construct(array $settings, int $odds = 1)
    {
        $this->settings = $settings;
        $this->odds = $odds;
        $this->client = new Client($this->settings['username'],$this->settings['pass']);
    }

    /**
     * Get fixtures
     * @param string $sort
     * @return array
     */
    public function getFixtures(string $sort = 'starts'): array
    {
        $data = json_decode($this->client->getFixtures($this->settings['fixture']), 1);

        /** validate data */
        $this->validate($data);

        /** prepare fixture fields */
        $this->prepareFields($data);

        /** relate fixture with odds */
        if($this->odds) {
            $odd = new Odd($this->settings);
            $fixtureOdd = new FixtureOdd($this->settings);
            $this->fixtures = $fixtureOdd->relate($this->fixtures, $odd->getOdds());
        }

        /** filter fixtures */
        $this->filter();

        /** sort fixtures */
        $this->sort($sort);

        return $this->fixtures;
    }

    /**
     * @param $data
     */
    private function validate($data)
    {
        if(!is_array($data['league'])) {
            echo 'Fixtures error';
            BaseHelper::outputArray($data);
            die;
        }
    }

    /**
     * Prepare fixture fields
     * @param $data
     * @return void
     */
    private function prepareFields($data): void
    {
        foreach ($data['league'] as $league) {
            foreach ($league['events'] as $fixture) {
                $fixture['tour'] = $this->getTour($league['name']);
                $fixture['round'] = $this->getRound($league['name']);
                $fixture['tournament'] = $this->getTournament($league['name'], $fixture['tour'], $fixture['round']);
                $fixture['starts'] = strtotime($fixture['starts']);
                $fixture['o_starts'] = BaseHelper::outputDate($fixture['starts']);
                $fixture = array_merge($fixture, $this->settings['fixture']);
                $this->fixtures[$fixture['id']] = $fixture;
            }
        }
    }

    /**
     * Filter fixtures
     * @return void
     */
    private function filter(): void
    {
        $this->fixtures = array_filter($this->fixtures, function ($fixture){
            return $fixture['starts'] > time() && $fixture['status'] != 'H';
        });
    }

    /**
     * Sort fixtures
     * @param $sort
     * @return void
     */
    private function sort($sort): void
    {
        uasort($this->fixtures, function($a, $b) use ($sort) {
            return strcmp($a[$sort], $b[$sort]);
        });
    }

    /**
     * @param string $name
     * @return string
     */
    private function getTour(string $name): string
    {
        /** get tour */
        foreach (PS3838::TOUR as $tour) {
            if(preg_match("#{$tour}.*#i", $name)) {
                return $tour;
            }
        }

        return '';
    }

    /**
     * @param string $name
     * @return string
     */
    private function getRound(string $name): string
    {
        return trim(current(array_reverse(explode('-', $name))));
    }

    /**
     * @param string $name
     * @param string $tour
     * @param string $round
     * @return string
     */
    private function getTournament(string $name, string $tour, string $round): string
    {
        $pattern = "#^{$tour}(.+)*-\s*{$round}$#i";
        if(!preg_match($pattern, $name, $matches)) return $name;
        return trim($matches[1]);
    }

}