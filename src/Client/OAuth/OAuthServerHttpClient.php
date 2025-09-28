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
		$data = $this->get("id-tokens/verify/{$idToken}");
		$payload = new IdTokenPayload();
		$payload->setData($data);
		return $payload;
	}

	public function requestIdTokenFromLogin(RequestIdTokenFromLoginPayload $request): IdTokenPayload {
		$data = $this->post('id-tokens/from-login', $request);
		$payload = new IdTokenPayload();
		$payload->setData($data);
		return $payload;
	}

	public function refreshIdToken(RequestIdTokenFromPrevTokenPayload $request): IdTokenPayload {
		$data = $this->post('id-tokens/refresh', $request);
		$payload = new IdTokenPayload();
		$payload->setData($data);
		return $payload;
	}

	public function requestAccessToken(RequestAccessTokenPayload $request): AccessTokenPayload {
		$data = $this->post('access-tokens/from-id-token', $request);
		$payload = new AccessTokenPayload();
		$payload->setData($data);
		return $payload;
	}
}
