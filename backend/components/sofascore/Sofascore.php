<?php

namespace backend\components\sofascore;


use backend\components\sofascore\models\TennisEvent;
use backend\components\sofascore\services\Client;
use yii\base\Component;

class Sofascore extends Component
{

    /**
     * @var Client
     */
    private $client;

    /**
     * Sofascore constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->client = new Client();

        parent::__construct($config);
    }

    /**
     * @param null $data
     * @return array
     */
    public function getTennis($data = null): array
    {
        $events = json_decode($this->client->getTennis($data), 1);
        if(empty($events['events'])) return $events;

        $tennisEvent = new TennisEvent($events['events']);
        return $tennisEvent->getEvents();
    }

}