<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class DateTimeHelper {

	public static array $formats = [
		'Y-m-d\TH:i:s.uP', /* ISO with microseconds and offset */
		'Y-m-d\TH:i:sT', /* ISO with time zone */
		'Y-m-d\TH:i:sP', /* ISO with offset */
		DateTimeInterface::RFC7231, /* http dates */
		DateTimeInterface::RSS,
	];

	public static function toTimezone(DateTimeInterface $date, string $timeZoneName): DateTimeInterface {
		$gmtdate = DateTime::createFromInterface($date);
		$gmtdate->setTimezone(new DateTimeZone($timeZoneName));
		return $gmtdate;
	}

	public static function toGmt(DateTimeInterface $date): DateTimeInterface {
		return self::toTimezone($date, 'GMT');
	}

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

	public static function format(?DateTimeInterface $date, ?string $format = null): ?string {
		if ($date === null) return null;
		if ($format === null) $format = self::$formats[0];
		return $date->format($format);
	}

	public static function formatForJson(DateTimeInterface $date): ?string {
		return self::format($date, DateTimeInterface::RFC3339_EXTENDED);
	}

	public static function formatAsGmt(DateTimeInterface $date, string $format): string {
		return self::format(self::toGmt($date), $format);
	}

	public static function formatForHttp(DateTimeInterface $date): string {
		return self::formatAsGmt($date, DateTimeInterface::RFC7231);
	}

	public static function formatForRss(DateTimeInterface $date): string {
		return self::formatAsGmt($date, DateTimeInterface::RSS);
	}

	public static function year(?DateTimeInterface $date = null): int {
		if ($date === null) return self::year(new DateTimeImmutable());
		return intval($date->format("Y"));
	}

	public static function month(?DateTimeInterface $date = null): int {
		if ($date === null) return self::month(new DateTimeImmutable());
		return intval($date->format("n"));
	}

	public static function day(?DateTimeInterface $date = null): int {
		if ($date === null) return self::day(new DateTimeImmutable());
		return intval($date->format("j"));
	}

}
