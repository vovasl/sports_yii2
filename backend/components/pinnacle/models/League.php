<?php

namespace backend\components\pinnacle\models;


use backend\components\pinnacle\helpers\BaseHelper;
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

        var_dump($data);
        die;

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
        if(empty($leagues['leagues'])) return $data;
        foreach ($leagues['leagues'] as $league) {
            if($league['eventCount'] == 0) continue;

            /** check tour */
            if(!preg_match("#{$this->settings['base']['tour']}.*#i", $league['name'])) continue;

            /** doubles and mixed events */
            if(preg_match('#doubles|mixed#i', $league['name'])) continue;

            /** parse tournament name */
            if(!$tournament = $this->parseTennisTournamentName($league['name'])) continue;

            $data[] = [
                'sportid' => $this->settings['base']['sportid'],
                'leagueids' => $league['id'],
                'tournament' => $tournament[0],
                'round' => $tournament[1],
                'tour' => $tournament[2],
            ];
        }

        //die;

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
        //$data = array_map('trim', explode('-', $name));
        $data = array_map('trim', explode(' - ', $name));

        /** exception for Davis Cup - without tournament name */
        if(empty($data[0]) && !empty($tour)) $data[0] = $tour;

        /** add tour info */
        if(!empty($tour)) $data[] = $tour;

        if(count($data) != 3) {
            //::log can't parse tournament name
            return false;
        }
        return $data;
    }
}