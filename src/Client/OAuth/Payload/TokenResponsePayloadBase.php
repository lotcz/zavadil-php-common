<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload;

use DateTimeInterface;
use Zavadil\Common\Client\Payload\PayloadBase;

class TokenResponsePayloadBase extends PayloadBase {

	public ?string $token = null;

	public ?DateTimeInterface $issuedAt = null;

	public ?DateTimeInterface $expires = null;
}
