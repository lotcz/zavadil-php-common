<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload\Request;

class RequestAccessTokenPayload extends TokenRequestPayloadBase {

	public string $refreshToken;

	public string $privilege;

	public function __construct(string $targetAudience, string $refreshToken, string $privilege = '*') {
		parent::__construct($targetAudience);
		$this->refreshToken = $refreshToken;
		$this->privilege = $privilege;
	}
}
