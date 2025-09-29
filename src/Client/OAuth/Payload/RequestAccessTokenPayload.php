<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload;

class RequestAccessTokenPayload extends TokenRequestPayloadBase {

	public ?string $idToken = null;

	public ?string $privilege = null;

}
