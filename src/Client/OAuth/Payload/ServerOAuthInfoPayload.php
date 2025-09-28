<?php

namespace Zavadil\Common\Client\OAuth\Payload;

use Zavadil\Common\Client\PayloadBase;

class ServerOAuthInfoPayload extends PayloadBase {

	public bool $debugMode;

	public string $targetAudience;

	public string $oauthServerUrl;

	public string $version;
}
