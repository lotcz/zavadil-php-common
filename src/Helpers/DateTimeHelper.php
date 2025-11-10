<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class DateTimeHelper {

	public const string FORMAT_JSON = 'Y-m-d\TH:i:s.uP';

	public static array $formats = [
		self::FORMAT_JSON, /* ISO with microseconds and offset */
		'Y-m-d\TH:i:sT', /* ISO with time zone */
		DateTimeInterface::W3C, /* ISO with offset */
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

	public static function formatAsGmt(DateTimeInterface $date, string $format): string {
		return self::format(self::toGmt($date), $format);
	}

	public static function formatForJson(DateTimeInterface $date): ?string {
		return self::format($date, self::FORMAT_JSON);
	}

	public static function formatForSitemap(DateTimeInterface $date): string {
		return self::format($date, DateTimeInterface::W3C);
	}

	public static function formatForHttp(DateTimeInterface $date): string {
		return self::formatAsGmt($date, DateTimeInterface::RFC7231);
	}

	public static function formatForRss(DateTimeInterface $date): string {
		return self::format($date, DateTimeInterface::RSS);
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
