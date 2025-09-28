<?php

namespace Zavadil\Common\Client\OAuth\Payload;

class RequestIdTokenFromLoginPayload extends TokenRequestPayloadBase {

	public string $login;

	public string $password;
}
