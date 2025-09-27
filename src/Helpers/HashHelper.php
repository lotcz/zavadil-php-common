<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class HashHelper {

    public static function crc32hex(string $str): string {
        return dechex(crc32($str));
    }

    public static function fileHash(string $path): string {
        return hash_file('md5', $path);
    }
}
