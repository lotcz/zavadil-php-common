<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\Payload;

use DateTimeInterface;

class EntityBase extends PayloadBase {

	public ?int $id = null;

	public ?DateTimeInterface $createdOn = null;

	public ?DateTimeInterface $lastUpdatedOn = null;

}
