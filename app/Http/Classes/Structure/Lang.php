<?php

namespace App\Http\Classes\Structure;

final class Lang
{
    public const RUS = 'rus';
    public const UKR = 'ukr';
    public const ENG = 'eng';
    public const GEO = 'geo';

    public const ARRAY_LANG = [
        self::RUS,
        self::UKR,
        self::ENG,
        self::GEO,
    ];

    public static function toOldLocale(string $lang): string
    {
        return substr($lang, 0, 2);
    }
}
