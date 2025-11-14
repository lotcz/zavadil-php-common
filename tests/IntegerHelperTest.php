<?php

namespace Zavadil\Common\Tests;

use PHPUnit\Framework\TestCase;
use Zavadil\Common\Helpers\IntegerHelper;

class IntegerHelperTest extends TestCase {

	public function testParse() {
		$this->assertEquals(null, IntegerHelper::parse(null));
		$this->assertEquals(null, IntegerHelper::parse(''));
		$this->assertEquals(null, IntegerHelper::parse('abc'));
		$this->assertEquals(0, IntegerHelper::parse('0'));
		$this->assertEquals(0, IntegerHelper::parse('0 '));
		$this->assertEquals(184, IntegerHelper::parse(' 184'));
		$this->assertEquals(-5, IntegerHelper::parse('-5'));
		$this->assertEquals(111, IntegerHelper::parse(null, 111));
		$this->assertEquals(111, IntegerHelper::parse('', 111));
		$this->assertEquals(111, IntegerHelper::parse('fs', 111));
	}
}
