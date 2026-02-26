<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload\Request;

class RequestRefreshTokenFromLoginPayload extends TokenRequestPayloadBase {

	public string $login;

	public string $password;

	public function __construct(string $targetAudience, string $login, string $password) {
		parent::__construct($targetAudience);
		$this->login = $login;
		$this->password = $password;
	}
}
