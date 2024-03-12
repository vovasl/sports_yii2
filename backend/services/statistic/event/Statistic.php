<?php

namespace backend\services\statistic\event;

use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use backend\services\statistic\event\total\FavoritePlayer;
use backend\services\statistic\event\total\OverPlayer;
use backend\services\statistic\event\total\Player;

class Statistic
{

    private $settings;
    private $data;

    /**
     * Statistic constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->settings = $this->initSettings($event);
        $this->process();
    }

    /**
     * @param Event $event
     * @return array
     */
    private function initSettings(Event $event): array
    {
        return [
            'total' => [
                'over' => [
                    new OverPlayer($event, Odd::ADD_TYPE['over']),
                    new Player($event, Odd::ADD_TYPE['over']),
                    new FavoritePlayer($event, Odd::ADD_TYPE['over'])
                ],
                'under' => [],
            ]
        ];
    }

    private function process()
    {
        foreach ($this->settings as $type => $dataType) {
            foreach ($dataType as $addType => $handlers) {
                foreach ($handlers as $handler) {
                    $data = $handler->getData();
                    if(count($data) == 0) continue;
                    $this->data[$type][$addType][] = $data;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

}