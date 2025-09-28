<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Client\OAuth\Payload\AccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\IdTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestAccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestIdTokenFromLoginPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestIdTokenFromPrevTokenPayload;
use Zavadil\Common\Helpers\PathHelper;

class OAuthServerHttpClient extends HttpClient {

	public function __construct(string $oauthUrl) {
		parent::__construct(PathHelper::of($oauthUrl, "/api/oauth"));
	}

	public function jwks() {
		return $this->get('jwks.json');
	}

	public function verifyIdToken(string $idToken): IdTokenPayload {
		return $this->get("id-tokens/verify/{$idToken}", null, IdTokenPayload::class);
	}

	public function requestIdTokenFromLogin(RequestIdTokenFromLoginPayload $request): IdTokenPayload {
		return $this->post('id-tokens/from-login', $request, null, IdTokenPayload::class);
	}

	public function refreshIdToken(RequestIdTokenFromPrevTokenPayload $request): IdTokenPayload {
		return $this->post('id-tokens/refresh', $request, null, IdTokenPayload::class);
	}

	public function requestAccessToken(RequestAccessTokenPayload $request): AccessTokenPayload {
		return $this->post('access-tokens/from-id-token', $request, null, AccessTokenPayload::class);
	}
}
