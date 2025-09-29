<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload;

use Zavadil\Common\Client\Payload\PayloadBase;

class RequestIdTokenFromPrevTokenPayload extends PayloadBase {

	public ?string $idToken = null;

}
