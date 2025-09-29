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
	
	static function slugify(string $str, string $encoding = 'UTF-8'): string {
		if (StringHelper::isBlank($str)) return '';
		$result = StringHelper::trimSpecial($str);
		$result = StringHelper::transliterate($result, $encoding);
		$result = strtolower($result);
		$result = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $result);
		$result = preg_replace("/[_| -\/]+/", '-', $result);
		return $result;
	}
}
