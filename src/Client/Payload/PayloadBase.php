<?php

namespace Zavadil\Common\Client\Payload;

use DateTimeInterface;
use ReflectionProperty;
use Zavadil\Common\Helpers\DateTimeHelper;
use Zavadil\Common\Helpers\StringHelper;

class PayloadBase {

	public function setData(mixed $data) {
		if (!(is_object($data) || is_array($data))) return;

		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$rp = new ReflectionProperty($this, $key);
				$type = $rp->getType()?->getName();

				if ($type === DateTimeInterface::class && is_string($value) && StringHelper::notBlank($value)) {
					$this->$key = DateTimeHelper::parse($value, true);
				} else {
					$this->$key = $value;
				}
			}
		}
	}
}
