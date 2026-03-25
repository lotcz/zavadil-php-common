<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class ImagezHelper {

	public static function hashToken(string $tokenRaw): string {
		return HashHelper::crc32hex($tokenRaw);
	}

	public static function createTokenRaw(
		string $secretToken,
		string $imageName,
		int $width,
		int $height,
		string $type = "fit",
		?string $ext = null,
		?string $vertical = null,
		?string $horizontal = null,
		?int $page = null
	): string {
		$base = "$secretToken-$imageName-$width-$height-$type";
		if (StringHelper::notBlank($ext)) {
			$base .= "-$ext";
		}
		if (StringHelper::notBlank($vertical)) {
			$base .= "-$vertical";
		}
		if (StringHelper::notBlank($horizontal)) {
			$base .= "-$horizontal";
		}
		if ($page !== null) {
			$base .= "-$page";
		}
		return $base;
	}

	public static function createTokenHash(
		string $secretToken,
		string $imageName,
		int $width,
		int $height,
		string $type = "fit",
		?string $ext = null,
		?string $vertical = null,
		?string $horizontal = null,
		?int $page = null
	): string {
		return ImagezHelper::hashToken(
			ImagezHelper::createTokenRaw($secretToken, $imageName, $width, $height, $type, $ext, $vertical, $horizontal, $page)
		);
	}

	static function validateTokenHash(string $tokenHash, string $tokenRaw): bool {
		return ImagezHelper::hashToken($tokenRaw) === $tokenHash;
	}

	public static function validateToken(
		string $tokenHash,
		string $secretToken,
		string $imageName,
		int $width,
		int $height,
		string $type = "fit",
		?string $ext = null,
		?string $vertical = null,
		?string $horizontal = null,
		?int $page = null
	): bool {
		return ImagezHelper::validateTokenHash(
			$tokenHash,
			ImagezHelper::createTokenRaw($secretToken, $imageName, $width, $height, $type, $ext, $vertical, $horizontal, $page)
		);
	}

	public static function getOriginalImageUrl(string $imagezBaseUrl, string $imageName): string {
		return UrlHelper::of($imagezBaseUrl, "images/original/$imageName");
	}

	public static function getResizedImageUrl(
		string $imagezBaseUrl,
		string $secretToken,
		string $imageName,
		int $width,
		int $height,
		string $type = "fit",
		?string $ext = null,
		?string $verticalAlign = null,
		?string $horizontalAlign = null,
		?int $page = null
	): string {
		$hash = ImagezHelper::createTokenHash($secretToken, $imageName, $width, $height, $type, $ext, $verticalAlign, $horizontalAlign, $page);
		return UrlHelper::of(
			$imagezBaseUrl,
			"images/resized/$imageName",
			[
				"token" => $hash,
				"width" => $width,
				"height" => $height,
				"type" => $type,
				"ext" => $ext,
				"v" => $verticalAlign,
				"h" => $horizontalAlign,
			]
		);
	}
}
