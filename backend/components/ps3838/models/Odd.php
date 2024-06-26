<?php

namespace backend\components\ps3838\models;


use backend\components\pinnacle\helpers\BaseHelper;
use backend\components\ps3838\services\Client;

class Odd
{
    CONST UPDATE_FIELD_SUFFIX = 'UpdatedAt';

    /**
     * @var array
     */
    private $settings;

    /**
     * @var Client
     */
    private $client;

    /**
     * Odd constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
        $this->client = new Client($this->settings['username'],$this->settings['pass']);
    }

    /**
     * Get odds
     * @return array
     */
    public function getOdds(): array
    {
        $options = $this->settings['fixture'];
        $options['oddsFormat'] = $this->settings['odds_format'];

        $data = json_decode($this->client->getOdds($options), 1);

        /** validate data */
        if(!$this->validate($data)) return [];

        /** prepare odd fields */
        $data = $this->prepareFields($data);

        return $data;
    }

    /**
     * @param $data
     * @return bool
     */
    private function validate($data): bool
    {
        if(!isset($data['leagues'])) {
            return false;
        }

        return true;
    }

    /**
     * Prepare odd fields
     * @param $data
     * @return array
     */
    private function prepareFields($data): array
    {
        $odds = [];
        foreach ($data['leagues'] as $league) {
            foreach ($league['events'] as $event) {
                foreach ($event['periods'] as $period) {
                    $period = $this->prepareDateFields($period);
                    $odds[$event['id']][] = $period;
                }
            }
        }

        return $odds;
    }

    /**
     * Prepare date fields
     * @param $period
     * @return mixed
     */
    private function prepareDateFields($period)
    {
        $fields = ['moneyline', ['spread' => 'spreads'], ['total' => 'totals'], 'teamTotal'];

        foreach ($fields as $field) {
            $name = (is_array($field)) ? key($field) : $field;
            $fieldName = "{$name}" . self::UPDATE_FIELD_SUFFIX;
            if (isset($period[$fieldName])) {
                $val = strtotime($period[$fieldName]);

                /** rename field */
                $fieldName = (is_array($field)) ? $field[$name] . self::UPDATE_FIELD_SUFFIX : $fieldName;

                $period[$fieldName] = $val;
                $period["o_$fieldName"] = BaseHelper::outputDate($val);
            }
        }
        return $period;
    }

}