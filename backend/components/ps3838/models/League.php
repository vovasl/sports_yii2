<?php

namespace backend\components\ps3838\models;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\ps3838\PS3838;
use backend\components\ps3838\services\Client;

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

        /** validate data */
        $this->validate($data);

        switch ($this->settings['base']['sportId']) {
            case PS3838::TENNIS:
                return $this->getTennis($data);
            default:
                return $data;
        }
    }

    /**
     * @param $data
     */
    private function validate($data)
    {
        if(!is_array($data['leagues'])) {
            echo 'Leagues error';
            BaseHelper::outputArray($data);
            die;
        }
    }

    /**
     * @param $leagues
     * @return array
     */
    private function getTennis($leagues): array
    {
        $ids= [];
        foreach ($leagues['leagues'] as $league) {

            /** no events */
            if($league['eventCount'] == 0) continue;

            /** check tour */
            if(!preg_match("#{$this->settings['base']['tour']}.*#i", $league['name'])) continue;

            /** doubles or mixed events */
            if(preg_match('#doubles|mixed#i', $league['name'])) continue;

            $ids[] = $league['id'];
        }

        //die;

        return $ids;
    }

}