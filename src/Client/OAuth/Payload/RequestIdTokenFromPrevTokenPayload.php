<?php

namespace Zavadil\Common\Client\OAuth\Payload;

use Zavadil\Common\Client\PayloadBase;

class RequestIdTokenFromPrevTokenPayload extends PayloadBase {

	public ?string $idToken = null;

}
