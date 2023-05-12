<?php

namespace backend\components\pinnacle\models;


use backend\components\pinnacle\Pinnacle;
use backend\components\pinnacle\services\Client;

class League
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
     * League constructor.
     * @param $client
     * @param array $settings
     * @param int $odds
     */
    public function __construct($client, array $settings, int $odds = 1)
    {
        $this->client = $client;
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getLeagues(): array
    {
        $data = json_decode($this->client->getLeagues($this->settings['fixture']),1);

        switch ($this->settings['fixture']['sportid']) {
            case Pinnacle::TENNIS:
                return $this->getTennis($data);
            default:
                return $data;
        }
    }

    /**
     * @param $leagues
     * @return array
     */
    private function getTennis($leagues): array
    {
        $data = [];
        //$pattern = "#{$this->settings['fixture']['tour']} [^chalenger|125k].*#i";
        $pattern = "#{$this->settings['fixture']['tour']} [^125k].*#i";
        foreach ($leagues['leagues'] as $league) {
            if($league['eventCount'] == 0) continue;
            if(!preg_match($pattern, $league['name'])) continue;
            if(!$tournament = $this->parseTennisTournamentName($league['name'])) continue;
            if($tournament[1] == 'Doubles') continue;

            $data[] = [
                'sportid' => $this->settings['fixture']['sportid'],
                'leagueids' => $league['id'],
                'tour' => $this->settings['fixture']['tour'],
                'tournament' => $tournament[0],
                'round' => $tournament[1]
            ];
        }

        return $data;
    }

    /**
     * @param $name
     * @return array|false
     */
    private function parseTennisTournamentName($name)
    {
        $name = trim(str_replace($this->settings['fixture']['tour'], '', $name));
        $data = array_map('trim', explode('-', $name));
        if(count($data) != 2) {
            //::log can't parse tournament name
            return false;
        }
        return $data;
    }
}