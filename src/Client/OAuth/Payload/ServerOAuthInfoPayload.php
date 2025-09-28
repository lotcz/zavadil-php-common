<?php

namespace Zavadil\Common\Client\OAuth\Payload;

use Zavadil\Common\Client\Payload\PayloadBase;

class ServerOAuthInfoPayload extends PayloadBase {

	public bool $debugMode;

	public string $targetAudience;

	public string $oauthServerUrl;

	public string $version;
}
