<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth;

use Zavadil\Common\Client\OAuth\Payload\Request\RenewRefreshTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Request\RequestAccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Request\RequestRefreshTokenFromLoginPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\AccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\Token\RefreshTokenPayload;
use Zavadil\Common\Helpers\OAuthHelper;

class OAuthTokenManager {

	private string $audience;

	private string $login;

	private string $password;

	private OAuthServerHttpClient $oAuthServer;

	private ?RefreshTokenPayload $refreshToken = null;

	private array $accessTokens = [];

	public function __construct(string $oAuthServerBaseUrl, string $targetAudience, string $login, string $password) {
		$this->audience = $targetAudience;
		$this->login = $login;
		$this->password = $password;
		$this->oAuthServer = new OAuthServerHttpClient($oAuthServerBaseUrl);
	}

	private function hasValidRefreshToken(): bool {
		return OAuthHelper::isValidToken($this->refreshToken);
	}

	private function getExistingAccessToken(string $privilege): ?AccessTokenPayload {
		if (!isset($this->accessTokens[$privilege])) return null;
		return $this->accessTokens[$privilege];
	}

	private function login(): RefreshTokenPayload {
		$this->reset();
		$payload = new RequestRefreshTokenFromLoginPayload($this->audience, $this->login, $this->password);
		$this->refreshToken = $this->oAuthServer->requestRefreshTokenFromLogin($payload);
		return $this->refreshToken;
	}

	private function renewRefreshToken(): RefreshTokenPayload {
		$payload = new RenewRefreshTokenPayload($this->getRefreshTokenRaw());
		$this->refreshToken = $this->oAuthServer->renewRefreshToken($payload);
		return $this->refreshToken;
	}

	public function reset(): void {
		$this->refreshToken = null;
		$this->accessTokens = [];
	}

	/**
	 * Get refresh token, renew it if needed
	 */
	public function getRefreshToken(): RefreshTokenPayload {
		if (!$this->hasValidRefreshToken()) return $this->login();
		if (OAuthHelper::isTokenReadyForRefresh($this->refreshToken)) return $this->renewRefreshToken();
		return $this->refreshToken;
	}

	public function getRefreshTokenRaw(): string {
		$idToken = $this->getRefreshToken();
		return $idToken->token;
	}

	public function verifyRefreshToken(string $token): RefreshTokenPayload {
		return $this->oAuthServer->verifyRefreshToken($token);
	}

	/**
	 * Get access token, refresh it if needed
	 */
	public function getAccessToken(string $privilege): AccessTokenPayload {
		$existing = $this->getExistingAccessToken($privilege);
		if (OAuthHelper::isValidToken($existing) && !OAuthHelper::isTokenReadyForRefresh($existing)) {
			return $existing;
		}

		$payload = new RequestAccessTokenPayload($this->audience, $this->getRefreshTokenRaw(), $privilege);

		$accessToken = $this->oAuthServer->requestAccessToken($payload);
		$this->accessTokens[$privilege] = $accessToken;
		return $accessToken;
	}

	public function getAccessTokenRaw(string $privilege): string {
		$accessToken = $this->getAccessToken($privilege);
		return $accessToken->token;
	}

}
