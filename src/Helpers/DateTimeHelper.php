<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class DateTimeHelper {

	public static string $format = 'Y-m-d\TH:i:s.uT';

	public static function parse(?string $str, bool $immutable = false): ?DateTimeInterface {
		if (StringHelper::isBlank($str)) return null;
		// remove extra digits that PHP cannot use
		$input = preg_replace('/\.(\d{6})\d*Z$/', '.$1Z', $str);
		$date = $immutable ? DateTimeImmutable::createFromFormat(self::$format, $input)
			: DateTime::createFromFormat(self::$format, $input);
		if ($date === false) {
			throw new Exception("Datetime value '$str' is invalid");
		}
		return $date;
	}

	public static function format(?DateTimeInterface $date): ?string {
		if ($date === null) return null;
		return $date->format(self::$format);
	}

}
