<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class IntegerHelper {

	public static function parse(?string $str, ?int $default = null): ?int {
		$trimmed = StringHelper::trim($str);
		if ($trimmed === '') return $default;
		if ($trimmed === '0') return 0;
		$result = intval($trimmed);
		if ($result === 0) return $default;
		return $result;
	}

}
