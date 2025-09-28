<?php

namespace Zavadil\Common\Tests;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Zavadil\Common\Client\Payload\PageBase;
use Zavadil\Common\Client\Payload\PayloadBase;
use Zavadil\Common\Helpers\DateTimeHelper;
use Zavadil\Common\Helpers\JsonHelper;

class JsonHelperPayloadTestClass extends PayloadBase {
	public string $stringField;
	public int $intField;
	public ?DateTimeInterface $dateField;
	public ?JsonHelperPayloadTestClass $jsonHelperTest;
}

class JsonHelperPayloadTestPage extends PageBase {

	/**
	 * @var array of JsonHelperPayloadTestClass
	 */
	public array $content;

	public function getContentClass(): string {
		return JsonHelperPayloadTestClass::class;
	}

}

class JsonHelperAndPayloadTest extends TestCase {

	// ENCODE

	public function testEncodePage() {
		$page = new JsonHelperPayloadTestPage();
		$page->totalItems = 100;
		$page->pageSize = 10;
		$page->pageNumber = 0;

		for ($i = 0; $i < $page->pageSize; $i++) {
			$obj = new JsonHelperPayloadTestClass();
			$obj->stringField = "test";
			$obj->intField = 10;
			$obj->dateField = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
			$page->content[] = $obj;
		}

		$json = "{\"stringField\":\"test\",\"intField\":10,\"dateField\":\"2025-09-29T11:26:32.373208Z\"}";
		$content = [];
		for ($i = 0; $i < $page->pageSize; $i++) {
			$content[] = $json;
		}
		$jsonContent = implode(",", $content);
		$jsonPage = "{\"totalItems\":100,\"pageSize\":10,\"pageNumber\":0,\"content\":[$jsonContent]}";

		$encoded = JsonHelper::encode($page);

		$this->assertEquals($jsonPage, $encoded);
	}

	// DECODE

	public function testDecodePage() {
		$json = "{\"stringField\":\"test\",\"intField\":10,\"dateField\":\"2025-09-29T11:26:32.373208Z\"}";
		$content = [];
		for ($i = 0; $i < 10; $i++) {
			$content[] = $json;
		}
		$jsonContent = implode(",", $content);
		$jsonPage = "{\"totalItems\":100,\"pageSize\":10,\"pageNumber\":0,\"content\":[$jsonContent]}";

		$decoded = JsonHelper::decode($jsonPage, JsonHelperPayloadTestPage::class);

		$this->assertTrue($decoded instanceof JsonHelperPayloadTestPage);
		$this->assertEquals($decoded->pageNumber, 0);
		$this->assertEquals($decoded->pageSize, 10);
		$this->assertEquals($decoded->totalItems, 100);
		$this->assertEquals(count($decoded->content), 10);

		$obj2 = $decoded->content[5];
		$this->assertTrue($obj2 instanceof JsonHelperPayloadTestClass);
		$this->assertEquals($obj2->stringField, "test");
		$this->assertEquals($obj2->intField, 10);
		$date = DateTimeHelper::parse("2025-09-29T11:26:32.373208406Z");
		$this->assertEquals($date->getTimestamp(), $obj2->dateField->getTimestamp());
	}

}
