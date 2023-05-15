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
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->client = new Client($settings['username'], $settings['pass']);
    }

    /**
     * @return array
     */
    public function getLeagues(): array
    {

        $data = json_decode($this->client->getLeagues($this->settings['base']),1);

        switch ($this->settings['base']['sportid']) {
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
        foreach ($leagues['leagues'] as $league) {
            if($league['eventCount'] == 0) continue;
            if(!preg_match("#{$this->settings['base']['tour']}.*#i", $league['name'])) continue;
            if(!$tournament = $this->parseTennisTournamentName($league['name'])) continue;
            if($tournament[1] == 'Doubles') continue;

            $data[] = [
                'sportid' => $this->settings['base']['sportid'],
                'leagueids' => $league['id'],
                'tournament' => $tournament[0],
                'round' => $tournament[1],
                'tour' => $tournament[2],
            ];
        }

        return $data;
    }

    /**
     * Parse tournament name
     * @param $name
     * @return array|false
     */
    private function parseTennisTournamentName($name)
    {
        /** get tour */
        foreach (explode('|', $this->settings['base']['tour']) as $tour) {
            if(preg_match("#{$tour}.*#i", $name)) {
                $name = trim(str_replace($tour, '', $name));
                break;
            }
        }

        /** get tournament name and round */
        $data = array_map('trim', explode('-', $name));

        if(!empty($tour)) $data[] = $tour;
        if(count($data) != 3) {
            //::log can't parse tournament name
            return false;
        }
        return $data;
    }
}