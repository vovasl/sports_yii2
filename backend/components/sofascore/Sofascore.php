<?php

namespace backend\components\sofascore;


use backend\components\sofascore\services\Client;
use yii\base\Component;

class Sofascore extends Component
{

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
        return json_decode($this->client->getTennis($data), 1);
    }
}