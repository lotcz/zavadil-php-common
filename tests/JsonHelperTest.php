<?php

namespace Zavadil\Common\Tests;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Zavadil\Common\Helpers\DateTimeHelper;
use Zavadil\Common\Helpers\JsonHelper;

class JsonHelperTestClass {
	public string $stringField;
	public int $intField;
	public bool $boolField;
	public ?DateTimeInterface $dateField;
	public ?JsonHelperTestClass $jsonHelperTest;
}

class JsonHelperTest extends TestCase {

	// ENCODE

	public function testEncodeSimple() {
		$obj = new JsonHelperTestClass();
		$obj->stringField = "test";
		$obj->intField = 10;
		$obj->boolField = true;
		$obj->dateField = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");

		$json = "{\"stringField\":\"test\",\"intField\":10,\"boolField\":true,\"dateField\":\"2025-09-29T11:26:32.373208+00:00\"}";

		$encoded = JsonHelper::encode($obj);

		$this->assertEquals($json, $encoded);
	}

	public function testEncodeNested() {
		$obj = new JsonHelperTestClass();
		$obj->stringField = "testNested";
		$obj->intField = 123;
		$obj->boolField = false;
		$obj->dateField = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");

		$obj->jsonHelperTest = new JsonHelperTestClass();
		$obj->jsonHelperTest->stringField = "test";
		$obj->jsonHelperTest->intField = 10;
		$obj->jsonHelperTest->dateField = DateTimeHelper::parse("2025-10-29T11:26:32.373208406Z");

		$json = "{\"stringField\":\"test\",\"intField\":10,\"dateField\":\"2025-10-29T11:26:32.373208+00:00\"}";
		$jsonNested = "{\"stringField\":\"testNested\",\"intField\":123,\"boolField\":false,\"dateField\":\"2025-09-29T11:26:32.373208+00:00\",\"jsonHelperTest\":$json}";

		$encoded = JsonHelper::encode($obj);

		$this->assertEquals($jsonNested, $encoded);
	}

	public function testEncodeArray() {
		$arr = [];

		$obj1 = new JsonHelperTestClass();
		$obj1->stringField = "test1";
		$obj1->intField = 1;
		$obj1->dateField = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");

		$arr[] = $obj1;

		$obj2 = new JsonHelperTestClass();
		$obj2->stringField = "test2";
		$obj2->intField = 2;
		$obj2->dateField = DateTimeHelper::parse("2025-10-29T11:26:32.373208406Z");

		$arr[] = $obj2;

		$json1 = "{\"stringField\":\"test1\",\"intField\":1,\"dateField\":\"2025-09-29T11:26:32.373208+00:00\"}";
		$json2 = "{\"stringField\":\"test2\",\"intField\":2,\"dateField\":\"2025-10-29T11:26:32.373208+00:00\"}";
		$json = "[$json1,$json2]";

		$encoded = JsonHelper::encode($arr);

		$this->assertEquals($json, $encoded);
	}

	// DECODE

	public function testDecode() {
		$json = "{\"stringField\": \"test\",\"intField\": 10,\"boolField\": false,\"dateField\": \"2025-09-29T11:26:32.373208406Z\"}";

		$decoded = JsonHelper::decode($json, JsonHelperTestClass::class);

		$this->assertEquals("test", $decoded->stringField);
		$this->assertEquals(10, $decoded->intField);
		$this->assertTrue($decoded->boolField === false);

		$date = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $decoded->dateField->getTimestamp());
	}

	public function testDecodeNested() {
		$json = "{\"stringField\":\"test\",\"intField\":10,\"dateField\":\"2025-10-29T11:26:32.373208Z\"}";
		$jsonNested = "{\"stringField\":\"testNested\",\"intField\":123,\"dateField\":\"2025-09-29T11:26:32.373208Z\",\"jsonHelperTest\":$json}";

		$decoded = JsonHelper::decode($jsonNested, JsonHelperTestClass::class);

		$this->assertTrue($decoded instanceof JsonHelperTestClass);
		$this->assertEquals($decoded->stringField, "testNested");
		$this->assertEquals($decoded->intField, 123);
		$date = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $decoded->dateField->getTimestamp());

		$obj2 = $decoded->jsonHelperTest;
		$this->assertTrue($obj2 instanceof JsonHelperTestClass);
		$this->assertEquals($obj2->stringField, "test");
		$this->assertEquals($obj2->intField, 10);
		$date = DateTimeHelper::parse("2025-10-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $obj2->dateField->getTimestamp());
	}

	public function testDecodeArray() {
		$json1 = "{\"stringField\":\"test1\",\"intField\":1,\"dateField\":\"2025-09-29T11:26:32.373208406Z\"}";
		$json2 = "{\"stringField\":\"test2\",\"intField\":2,\"dateField\":\"2025-10-29T11:26:32.373208406Z\"}";
		$json = "[$json1,$json2]";

		$decoded = JsonHelper::decode($json, JsonHelperTestClass::class);

		$obj1 = $decoded[0];
		$this->assertTrue($obj1 instanceof JsonHelperTestClass);
		$this->assertEquals($obj1->stringField, "test1");
		$this->assertEquals($obj1->intField, 1);
		$date = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $obj1->dateField->getTimestamp());

		$obj2 = $decoded[1];
		$this->assertTrue($obj2 instanceof JsonHelperTestClass);
		$this->assertEquals($obj2->stringField, "test2");
		$this->assertEquals($obj2->intField, 2);
		$date = DateTimeHelper::parse("2025-10-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $obj2->dateField->getTimestamp());
	}
}
