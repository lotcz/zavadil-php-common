<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\OAuth;

use Zavadil\Common\Client\OAuth\Payload\AccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\IdTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestAccessTokenPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestIdTokenFromLoginPayload;
use Zavadil\Common\Client\OAuth\Payload\RequestIdTokenFromPrevTokenPayload;
use Zavadil\Common\Helpers\OAuthHelper;

class OAuthTokenManager {

	private string $audience;

	private string $login;

	private string $password;

	private OAuthServerHttpClient $oAuthServer;

	private ?IdTokenPayload $idToken = null;

	private array $accessTokens = [];

	public function __construct(string $oAuthServerBaseUrl, string $targetAudience, string $login, string $password) {
		$this->audience = $targetAudience;
		$this->login = $login;
		$this->password = $password;
		$this->oAuthServer = new OAuthServerHttpClient($oAuthServerBaseUrl);
	}

	private function hasValidIdToken(): bool {
		return OAuthHelper::isValidToken($this->idToken);
	}

	private function getExistingAccessToken(string $privilege): ?AccessTokenPayload {
		if (!isset($this->accessTokens[$privilege])) return null;
		return $this->accessTokens[$privilege];
	}

	private function login(): IdTokenPayload {
		$this->reset();
		$payload = new RequestIdTokenFromLoginPayload();
		$payload->targetAudience = $this->audience;
		$payload->login = $this->login;
		$payload->password = $this->password;
		$this->idToken = $this->oAuthServer->requestIdTokenFromLogin($payload);
		return $this->idToken;
	}

	private function refreshIdToken(): IdTokenPayload {
		$payload = new RequestIdTokenFromPrevTokenPayload();
		$payload->idToken = $this->idToken;
		$this->idToken = $this->oAuthServer->refreshIdToken($payload);
		return $this->idToken;
	}

	public function reset(): void {
		$this->idToken = null;
		$this->accessTokens = [];
	}

	/**
	 * Get id token, refresh it if needed
	 */
	public function getIdToken(): IdTokenPayload {
		if (!$this->hasValidIdToken()) return $this->login();
		if (OAuthHelper::isTokenReadyForRefresh($this->idToken)) return $this->refreshIdToken();
		return $this->idToken;
	}

	public function getIdTokenRaw(): string {
		$idToken = $this->getIdToken();
		return $idToken->token;
	}

	public function verifyIdToken(string $token): IdTokenPayload {
		return $this->oAuthServer->verifyIdToken($token);
	}

	/**
	 * Get access token, refresh it if needed
	 */
	public function getAccessToken(string $privilege): AccessTokenPayload {
		$existing = $this->getExistingAccessToken($privilege);
		if (OAuthHelper::isValidToken($existing) && !OAuthHelper::isTokenReadyForRefresh($existing)) {
			return $existing;
		}

		$payload = new RequestAccessTokenPayload();
		$payload->targetAudience = $this->audience;
		$payload->idToken = $this->getIdTokenRaw();
		$payload->privilege = $privilege;

		$accessToken = $this->oAuthServer->requestAccessToken($payload);
		$this->accessTokens[$privilege] = $accessToken;
		return $accessToken;
	}

	public function getAccessTokenRaw(string $privilege): string {
		$accessToken = $this->getAccessToken($privilege);
		return $accessToken->token;
	}

}
