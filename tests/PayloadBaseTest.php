<?php

namespace Zavadil\Common\Tests;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zavadil\Common\Client\PayloadBase;
use Zavadil\Common\Helpers\DateTimeHelper;

class PayloadTestClass extends PayloadBase {

	public string $stringField;

	public int $intField;

	public ?DateTimeInterface $dateField;
}

class PayloadBaseTest extends TestCase {

	public function testSetDataArray() {
		$data = [
			'stringField' => 'test',
			'intField' => 10,
			'dateField' => '2025-09-29T11:26:32.373208406Z'
		];

		$payload = new PayloadTestClass();
		$payload->setData($data);

		$this->assertEquals($data['stringField'], $payload->stringField);
		$this->assertEquals($data['intField'], $payload->intField);

		$date = DateTimeHelper::parse($data['dateField'], true);
		$this->assertEquals($date->getTimestamp(), $payload->dateField->getTimestamp());

	}

	public function testSetDataObject() {
		$data = new StdClass();
		$data->stringField = 'test';
		$data->intField = 10;
		$data->dateField = '2025-09-29T11:26:32.373208406Z';

		$payload = new PayloadTestClass();
		$payload->setData($data);

		$this->assertEquals($data->stringField, $payload->stringField);
		$this->assertEquals($data->intField, $payload->intField);

		$date = DateTimeHelper::parse($data->dateField, true);
		$this->assertEquals($date->getTimestamp(), $payload->dateField->getTimestamp());
	}

}
