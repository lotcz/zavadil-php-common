<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload\Request;

use Zavadil\Common\Client\Payload\PayloadBase;

class TokenRequestPayloadBase extends PayloadBase {

	public string $targetAudience;

	public function __construct(string $targetAudience) {
		$this->targetAudience = $targetAudience;
	}
}
