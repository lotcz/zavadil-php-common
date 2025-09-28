<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class DateTimeHelper {

	public static array $formats = [
		'Y-m-d\TH:i:s.uT',
		'Y-m-d\TH:i:sT',
	];


	public static function parse(?string $str, bool $immutable = false): ?DateTimeInterface {
		if (StringHelper::isBlank($str)) return null;
		// remove extra digits that PHP cannot use
		$input = preg_replace('/\.(\d{6})\d*Z$/', '.$1Z', $str);
		foreach (self::$formats as $format) {
			$date = $immutable ? DateTimeImmutable::createFromFormat($format, $input)
				: DateTime::createFromFormat($format, $input);
			if ($date !== false) {
				return $date;
			}
		}
		throw new Exception("Datetime value '$str' is invalid");
	}

	public static function format(?DateTimeInterface $date): ?string {
		if ($date === null) return null;
		return $date->format(self::$formats[0]);
	}

}
