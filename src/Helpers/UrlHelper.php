<?php

declare(strict_types=1);

namespace Zavadil\Common\Helpers;

class UrlHelper {

	public static function of(string $baseUrl, ?string $endpoint = null, ?array $queryParams = null): string {
		$url = PathHelper::of($baseUrl, $endpoint);
		if (!empty($queryParams)) {
			$query = http_build_query($queryParams);
			$url .= (str_contains($url, '?') ? '&' : '?') . $query;
		}
		return $url;
	}

}
