<?php

namespace backend\components\pinnacle\models;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\pinnacle\services\Client;

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
     * @param $client
     * @param array $settings
     * @param int $odds
     */
    public function __construct($client, array $settings, int $odds = 1)
    {
        $this->client = $client;
        $this->settings = $settings;
        $this->odds = $odds;
    }

    /**
     * Get fixtures
     * @param string $sort
     * @return array
     */
    public function getFixtures(string $sort = 'starts'): array
    {
        $data = json_decode($this->client->getFixtures($this->settings['fixture']), 1);

        /** prepare fixture fields */
        $this->prepareFields($data);

        /** relate fixture with odds */
        if($this->odds) {
            $odd = new Odd($this->client, $this->settings);
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
     * Prepare fixture fields
     * @param $data
     * @return void
     */
    private function prepareFields($data): void
    {
        if(isset($data['league'][0]['events'])) {
            foreach ($data['league'][0]['events'] as $fixture) {
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
            return $fixture['starts'] > time();
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

}