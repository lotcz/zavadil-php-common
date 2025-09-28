<?php

namespace Zavadil\Common\Helpers;

use Zavadil\Common\Client\OAuth\Payload\TokenResponsePayloadBase;

class OAuthHelper {

	public static function isValidToken(?TokenResponsePayloadBase $token): bool {
		return $token !== null && StringHelper::notBlank($token->token) && !self::isTokenExpired($token);
	}

	public static function isTokenExpired(?TokenResponsePayloadBase $token): bool {
		if ($token === null) return false;
		if ($token->expires === null) return false;
		return ($token->expires->getTimestamp() < time());
	}

	public static function isTokenReadyForRefresh(?TokenResponsePayloadBase $token): bool {
		if (!self::isValidToken($token)) return false;
		if ($token->expires === null) return false;
		$middle = ($token->issuedAt->getTimestamp() + $token->expires->getTimestamp()) / 2;
		return ($middle < time());
	}
}
