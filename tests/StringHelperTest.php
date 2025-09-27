<?php

namespace Zavadil\Common\Tests;

use PHPUnit\Framework\TestCase;
use Zavadil\Common\Helpers\StringHelper;

class StringHelperTest extends TestCase {

    public function testIsBlank() {
        $this->assertTrue(StringHelper::isBlank(null));
        $this->assertTrue(StringHelper::isBlank(''));
        $this->assertTrue(StringHelper::isBlank('  '));
        $this->assertFalse(StringHelper::isBlank('Ahoj'));
    }
}
