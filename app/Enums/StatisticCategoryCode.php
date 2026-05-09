<?php

namespace App\Enums;

enum StatisticCategoryCode: string
{
    case DSI = 'DSI';
    case IBS = 'IBS';
    case IMK = 'IMK';
    case KEK_KI = 'KEK/KI';

    public function label(): string
    {
        return match ($this) {
            self::DSI => 'DSI',
            self::IBS => 'IBS',
            self::IMK => 'IMK',
            self::KEK_KI => 'KEK/KI',
        };
    }
}
