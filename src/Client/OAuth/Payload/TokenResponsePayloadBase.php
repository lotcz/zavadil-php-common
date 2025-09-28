<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload;

use DateTimeInterface;

class TokenResponsePayloadBase {
	
	public ?string $token = null;

	public ?DateTimeInterface $issuedAt = null;

	public ?DateTimeInterface $expires = null;
}
