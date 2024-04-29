<?php

namespace backend\helpers;

class ClientHelper
{

    CONST USER_AGENT_FILE = 'user-agents.txt';

    /**
     * @return string
     */
    public static function getUserAgent(): string
    {
        $data = file_get_contents(__DIR__ . '/assets/' . self::USER_AGENT_FILE);
        $userAgents = explode("\n", $data);

        $key = array_rand($userAgents);
        return $userAgents[$key];
    }

}