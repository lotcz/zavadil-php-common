<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class StringHelper {

	public static function strlen(?string $str): int {
		if ($str === null) return 0;
		return strlen($str);
	}

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

	static function shorten(?string $str, int $len = 100, ?string $ellipsis = "..."): ?string {
		if (self::isBlank($str)) return null;
		if (self::strlen($str) > $len) {
			$length = $len - self::strlen($ellipsis);
			return substr($str, 0, $length) . $ellipsis;
		} else {
			return $str;
		}
	}

	static function ellipsis($str, $len = 100) {
		return self::shorten($str, $len);
	}
}
