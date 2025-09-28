<?php

namespace Zavadil\Common\Tests;

use PHPUnit\Framework\TestCase;
use Zavadil\Common\Helpers\DateTimeHelper;

class DateTimeHelperTest extends TestCase {

	public function testIsBlank() {
		$dateEmpty = DateTimeHelper::parse("");
		$this->assertTrue($dateEmpty === null);

		$dateNull = DateTimeHelper::parse(null);
		$this->assertTrue($dateNull === null);

		$dateNs = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
		$this->assertTrue($dateNs !== null);

		$dateShort = DateTimeHelper::parse("2025-09-29T11:26:32.373208Z");
		$this->assertTrue($dateShort !== null);

		$dateSecs = DateTimeHelper::parse("2025-09-29T11:26:32Z");
		$this->assertTrue($dateSecs !== null);

		$this->assertEquals($dateNs->getTimestamp(), $dateShort->getTimestamp());
		$this->assertEquals($dateNs->getTimestamp(), $dateSecs->getTimestamp());
	}
}
