<?php

namespace backend\models\odd;


use frontend\models\sport\Odd;

class Calculate
{

    CONST LOSS = -100;

    private $data = [];
    private $result = [];

    /**
     * Calculate constructor.
     * @param array $data
     * @param array $result
     */
    public function __construct(array $data, array $result)
    {
        $this->data = $data;
        $this->result = $result;
    }

    public function run()
    {
        /** calculate odds profit */
        foreach ($this->data as $odds) {

            if(!empty($odds->profit)) continue;
            $method = "{$odds->oddType->name}Odds";
            if(!method_exists($this, $method)) {
                // ::log add method {$method}
                continue;
            }
            $this->{$method}($odds, $this->result);
        }
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function moneylineOdds(Odd $odds, array $result): bool
    {
        $odds->profit = ($odds->player_id == $result['winner_id']) ? $this->calc($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function spreadsOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamSpreadHome'] : $result['teamSpreadAway'];
        $odds->profit = ($odds->value > $val) ? $this->calc($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function setsSpreadsOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamSetsSpreadHome'] : $result['teamSetsSpreadAway'];
        $odds->profit = ($odds->value > $val) ? $this->calc($odds->odd) : self::LOSS;
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function totalsOdds(Odd $odds, array $result): bool
    {
        $odds->profit = $this->totals($odds, $result['totals']);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function setsTotalsOdds(Odd $odds, array $result): bool
    {
        $odds->profit = $this->totals($odds, $result['setsTotals']);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param array $result
     * @return bool
     */
    private function teamTotalOdds(Odd $odds, array $result): bool
    {
        $val = ($odds->player_id == $result['home_id']) ? $result['teamTotalHome'] : $result['teamTotalAway'];
        $odds->profit = $this->totals($odds, $val);
        $odds->save();

        return true;
    }

    /**
     * @param Odd $odds
     * @param int $val
     * @return int
     */
    private function totals(Odd $odds, int $val): int
    {
        $profit = NULL;

        /** total over */
        if($odds->add_type == Odd::ADD_TYPE['over']) {
            $profit = $val > $odds->value ? $this->calc($odds->odd) : self::LOSS;
        }
        /** total under */
        else if($odds->add_type == Odd::ADD_TYPE['under']) {
            $profit = $val < $odds->value ? $this->calc($odds->odd) : self::LOSS;
        }
        /** total equal */
        if($odds->value == $val) $profit = 0;

        return $profit;
    }

    /**
     * @param int $odd
     * @return int
     */
    private function calc(int $odd): int
    {
        return $odd - 100;
    }
}