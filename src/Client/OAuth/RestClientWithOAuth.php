<?php

namespace Zavadil\Common\Client\OAuth;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Client\OAuth\Payload\ServerOAuthInfoPayload;

class RestClientWithOAuth extends HttpClient {

	private string $login;

	private string $password;

	private string $privilege;

	private ?ServerOAuthInfoPayload $serverInfo = null;

	private ?OAuthTokenManager $tokenManager = null;


	public function __construct(string $baseUrl, string $login, string $password, string $privilege = '*') {
		parent::__construct($baseUrl);
		$this->login = $login;
		$this->password = $password;
		$this->privilege = $privilege;
	}

	public function getServerInfo(): ServerOAuthInfoPayload {
		if ($this->serverInfo === null) {
			$insecureClient = new HttpClient($this->baseUrl);
			$data = $insecureClient->get("api/status/oauth/info");
			$this->serverInfo = new ServerOAuthInfoPayload();
			$this->serverInfo->setData($data);
		}
		return $this->serverInfo;
	}

	public function getTokenManager(): OAuthTokenManager {
		if ($this->tokenManager === null) {
			$info = $this->getServerInfo();
			$this->tokenManager = new OAuthTokenManager(
				$info->oauthServerUrl,
				$info->targetAudience,
				$this->login,
				$this->password
			);
		}
		return $this->tokenManager;
	}

	protected function getHeaders(): array {
		$headers = parent::getHeaders();
		$headers['Authorization'] = 'Bearer ' . $this->getTokenManager()->getAccessTokenRaw($this->privilege);
		return $headers;
	}
}
