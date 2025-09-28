<?php

namespace Zavadil\Common\Client\OAuth\Payload;

class ServerOAuthInfoPayload {

	public bool $debugMode;

	public string $targetAudience;

	public string $oauthServerUrl;

	public string $version;
}
