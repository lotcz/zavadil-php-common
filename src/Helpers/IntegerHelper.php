<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use RoundingMode;

class IntegerHelper {

	public static function parse(?string $str, ?int $default = null): ?int {
		$trimmed = StringHelper::trim($str);
		if ($trimmed === '') return $default;
		if ($trimmed === '0') return 0;
		$result = intval($trimmed);
		if ($result === 0) return $default;
		return $result;
	}

	public static function round(string|int|float|null $n, int $precision = 0, $mode = RoundingMode::HalfAwayFromZero): int|null {
		if ($n === null) return null;
		if (is_int($n)) return $n;
		if (is_string($n)) return self::round(self::parse($n), $precision, $mode);
		return (int)round($n, $precision, $mode);
	}

}
