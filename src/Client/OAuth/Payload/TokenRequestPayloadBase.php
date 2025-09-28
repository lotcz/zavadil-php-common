<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth\Payload;

use Zavadil\Common\Client\PayloadBase;

class TokenRequestPayloadBase extends PayloadBase {

	public ?string $targetAudience = null;

}
