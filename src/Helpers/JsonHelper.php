<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

use DateTimeInterface;
use ReflectionProperty;
use Zavadil\Common\Client\Payload\PayloadBase;

class JsonHelper {

	/**
	 * Normalize PHP values for safe JSON encoding.
	 * - DateTimeInterface => RFC3339 string (DATE_ATOM)
	 * - Arrays and stdClass are normalized recursively
	 * - Other scalars/objects are left as-is and rely on json_encode default behavior
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function normalizeForJson(mixed $value): mixed {
		if ($value instanceof DateTimeInterface) {
			return DateTimeHelper::format($value);
		}
		if (is_array($value)) {
			$normalized = [];
			foreach ($value as $k => $v) {
				$normalized[$k] = self::normalizeForJson($v);
			}
			return $normalized;
		}
		if (is_object($value)) {
			$vars = get_object_vars($value);
			foreach ($vars as $k => $v) {
				$vars[$k] = self::normalizeForJson($v);
			}
			return (object)$vars;
		}
		return $value;
	}

	public static function encode(mixed $data): string {
		$obj = self::normalizeForJson($data);
		return json_encode($obj, JSON_THROW_ON_ERROR, 512);
	}

	public static function hydrateToClass(?string $className, mixed $obj, $classUsed = false): mixed {
		if (is_object($obj) && $className !== null) {
			$instance = new $className();

			foreach ($obj as $key => $value) {
				if (property_exists($instance, $key)) {
					$rp = new ReflectionProperty($instance, $key);
					$type = $rp->getType()?->getName();
					if ($type === DateTimeInterface::class && is_string($value) && StringHelper::notBlank($value)) {
						$instance->$key = DateTimeHelper::parse($value, true);
					} else {
						$instance->$key = self::hydrateToClass($type, $value, true);
					}
				}
			}

			if ($instance instanceof PayloadBase) {
				$instance->hydrateData($obj);
			}

			return $instance;
		}

		if (is_array($obj)) {
			$arr = [];
			foreach ($obj as $key => $value) {
				$arr[$key] = self::hydrateToClass($classUsed ? null : $className, $value, true);
			}
			return $arr;
		}

		return $obj;
	}

	public static function decode(?string $data, ?string $className = null): mixed {
		if (StringHelper::isBlank($data)) return null;
		$obj = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
		return self::hydrateToClass($className, $obj);
	}
}
