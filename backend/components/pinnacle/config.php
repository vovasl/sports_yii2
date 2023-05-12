<?php

const TENNIS = 33;
const ATP = 'ATP';
const WTA = 'WTA';
const CONFIG = [
    'sports' => [
        TENNIS => [ //tennis
            'sets' => ['moneyline', 'spreads', 'totals'],
            'games' => ['spreads', 'totals', 'teamTotal'],
        ]
    ],
    'lines' => [
        'moneyline' => ['home', 'away'],
        'spreads' => ['hdp', 'home', 'away'],
        'totals' => ['points', 'over', 'under'],
        'teamTotal' => [
            'home' => ['points', 'over', 'under'],
            'away' => ['points', 'over', 'under']
        ]
    ]
];