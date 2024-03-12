<?php

namespace backend\services\statistic\event;

interface BaseInterface
{

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return array
     */
    public function getStatistic(): array;

    /**
     * @return bool
     */
    public function validateEvent(): bool;

    /**
     * @return array
     */
    public function getUrl(): array;

    /**
     * @param array $data
     * @return array
     */
    public function prepareStatistic(array $data): array;
}