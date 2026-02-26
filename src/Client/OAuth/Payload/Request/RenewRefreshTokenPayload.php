<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload\Request;

use Zavadil\Common\Client\Payload\PayloadBase;

class RenewRefreshTokenPayload extends PayloadBase {

	public string $refreshToken;

	public function __construct(string $refreshToken) {
		$this->refreshToken = $refreshToken;
	}

}
