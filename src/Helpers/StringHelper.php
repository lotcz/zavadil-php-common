<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class StringHelper {

    public static function isBlank(?string $str): bool {
        if ($str === null) return true;
        return (strlen(StringHelper::trim($str)) === 0);
    }

    public static function notBlank(?string $str): bool {
        return !self::isBlank($str);
    }

    public static function trim(?string $str, ?string $characters = null): string {
        if ($str === null) return '';
        return $characters === null ? trim($str) : trim($str, $characters);
    }
}
