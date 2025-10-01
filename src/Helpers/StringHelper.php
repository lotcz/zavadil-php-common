<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class StringHelper {

	public static function strlen(?string $str): int {
		if ($str === null) return 0;
		return mb_strlen($str);
	}

	public static function isBlank(?string $str): bool {
		if ($str === null) return true;
		return (self::strlen(StringHelper::trim($str)) === 0);
	}

	public static function notBlank(?string $str): bool {
		return !self::isBlank($str);
	}

	public static function trim(?string $str, ?string $characters = null): string {
		if ($str === null) return '';
		return $characters === null ? mb_trim($str) : mb_trim($str, $characters);
	}

	static function trimSpecial($s) {
		return self::trim($s, ' .,-*/?!\'"');
	}

	static array $czech_transliteration = [
		'á' => 'a', 'é' => 'e', 'ě' => 'e', 'í' => 'i', 'ý' => 'y', 'ó' => 'o', 'ú' => 'u', 'ů' => 'u', 'ž' => 'z', 'š' => 's', 'č' => 'c', 'ř' => 'r', 'ď' => 'd', 'ť' => 't', 'ň' => 'n',
		'Á' => 'A', 'É' => 'E', 'Ě' => 'E', 'Í' => 'I', 'Ý' => 'Y', 'Ó' => 'O', 'Ú' => 'U', 'Ů' => 'U', 'Ž' => 'Z', 'Š' => 'S', 'Č' => 'C', 'Ř' => 'R', 'Ď' => 'D', 'Ť' => 'T', 'Ň' => 'N'
	];

	static function transliterateCzech(string $str): string {
		$result = $str;
		foreach (self::$czech_transliteration as $czech => $ascii) {
			$result = str_replace($czech, $ascii, $result);
		}
		return $result;
	}

	static function transliterate(string $str, $encoding = 'UTF-8'): string {
		return iconv($encoding, "ASCII//TRANSLIT", self::transliterateCzech($str));
	}

	static function shorten(?string $str, int $len = 100, ?string $ellipsis = "..."): ?string {
		if (self::isBlank($str)) return null;
		if (self::strlen($str) > $len) {
			$length = $len - self::strlen($ellipsis);
			return mb_substr($str, 0, $length) . $ellipsis;
		} else {
			return $str;
		}
	}

	static function ellipsis(?string $str, int $len = 100): ?string {
		return self::shorten($str, $len);
	}
}
