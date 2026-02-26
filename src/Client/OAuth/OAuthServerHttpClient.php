<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Client\OAuth\Payload\Request\RenewRefreshTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Request\RequestAccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Request\RequestRefreshTokenFromLoginPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\AccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\IdTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\RefreshTokenPayload;
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

	public function verifyRefreshToken(string $refreshToken): RefreshTokenPayload {
		return $this->get("refresh-tokens/verify/{$refreshToken}", null, RefreshTokenPayload::class);
	}

	public function requestRefreshTokenFromLogin(RequestRefreshTokenFromLoginPayload $request): RefreshTokenPayload {
		return $this->post('refresh-tokens/from-login', $request, null, RefreshTokenPayload::class);
	}

	public function renewRefreshToken(RenewRefreshTokenPayload $request): RefreshTokenPayload {
		return $this->post('refresh-tokens/renew', $request, null, RefreshTokenPayload::class);
	}

	public function requestAccessToken(RequestAccessTokenPayload $request): AccessTokenPayload {
		return $this->post('access-tokens/from-refresh-token', $request, null, AccessTokenPayload::class);
	}
}
