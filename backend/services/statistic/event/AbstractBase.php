<?php

namespace backend\services\statistic\event;

abstract class AbstractBase
{

    /**
     * @return array
     */
    public abstract function getData(): array;

    /**
     * @return array
     */
    public function getStatistic(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public abstract function validateEvent(): bool;

    /**
     * @return array
     */
    public function getUrl(): array
    {
        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    public abstract function prepareStatistic(array $data): array;
}