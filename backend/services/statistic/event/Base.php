<?php

namespace backend\services\statistic\event;

use backend\services\statistic\event\AbstractBase;
use frontend\models\sport\Event;

class Base extends AbstractBase
{
    const TITLE = '';

    protected $event;
    protected $type;

    public function __construct(Event $event, string $type)
    {
        $this->event = $event;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $statistic = $this->getStatistic();
        if(count($statistic) == 0) return [];

        return [
            'title' => static::TITLE,
            'statistic' => $statistic,
            'url' => $this->getUrl(),
        ];
    }

    /**
     * @return bool
     */
    public function validateEvent(): bool
    {
        if(is_null($this->event->eventTournament->surface)) return false;

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareStatistic(array $data): array
    {
        foreach ($data as $k => $stat) {

            /** unset empty statistic */
            if($stat->count_events == 0) {
                unset($data[$k]);
            }
        }

        return $data;
    }

}