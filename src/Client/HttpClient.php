<?php

declare(strict_types=1);

namespace Zavadil\Common\Client;

use Zavadil\Common\Helpers\StringHelper;

/**
 * Simple cURL-based HTTP client implementing RestClient
 */
class HttpClient implements RestClient {

	protected string $baseUrl;

	protected int $timeout;

	public function __construct(string $baseUrl = '', int $timeout = 30) {
		$this->baseUrl = rtrim($baseUrl, '/');
		$this->timeout = $timeout;
	}

	/**
	 * Override this in inherited class to provide different headers
	 */
	protected function getHeaders(): array {
		return [
			'Accept' => 'application/json, */*;q=0.8',
			'Content-Type' => 'application/json; charset=utf-8'
		];
	}

	private function prepareHeaders(): array {
		$headers = $this->getHeaders();

		// Convert associative headers to cURL format
		$curlHeaders = [];
		foreach ($headers as $name => $value) {
			$curlHeaders[] = $name . ': ' . $value;
		}
		return $curlHeaders;
	}

	private function extractErrorMessage(string $body): ?string {
		if (StringHelper::isBlank($body)) {
			return null;
		}
		try {
			$data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
			if (is_array($data)) {
				foreach (['message', 'error', 'detail', 'description'] as $key) {
					if (isset($data[$key]) && is_string($data[$key])) {
						return $data[$key];
					}
				}
			}
			return $body;
		} catch (\JsonException $_) {
			return $body;
		}
	}

	/**
	 * Normalize PHP values for safe JSON encoding.
	 * - DateTimeInterface => RFC3339 string (DATE_ATOM)
	 * - Arrays and stdClass are normalized recursively
	 * - Other scalars/objects are left as-is and rely on json_encode default behavior
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	private function normalizeForJson(mixed $value): mixed {
		if ($value instanceof \DateTimeInterface) {
			return $value->format(DATE_ATOM);
		}
		if (is_array($value)) {
			$normalized = [];
			foreach ($value as $k => $v) {
				$normalized[$k] = $this->normalizeForJson($v);
			}
			return $normalized;
		}
		if ($value instanceof \stdClass) {
			$vars = get_object_vars($value);
			foreach ($vars as $k => $v) {
				$vars[$k] = $this->normalizeForJson($v);
			}
			return (object)$vars;
		}
		return $value;
	}

	private function buildUrl(string $endpoint, ?array $queryParams = []): string {
		$endpoint = ltrim($endpoint, '/');
		$url = $this->baseUrl . '/' . $endpoint;
		if (!empty($queryParams)) {
			$query = http_build_query($queryParams);
			$url .= (str_contains($url, '?') ? '&' : '?') . $query;
		}
		return $url;
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param array<string,mixed> $queryParams
	 * @param mixed $body
	 * @param int|null $outStatusCode Output parameter set to HTTP status code
	 * @return mixed Decoded response (if JSON) or raw string
	 * @throws \Exception on HTTP or cURL error
	 */
	private function request(string $method, string $endpoint, array $queryParams = [], mixed $body = null, ?int &$outStatusCode = null) {
		$url = $this->buildUrl($endpoint, $queryParams);

		$ch = curl_init();
		if ($ch === false) {
			throw new \Exception('Failed to initialize cURL');
		}

		$headers = $this->prepareHeaders();

		$options = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => $this->timeout,
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HEADER => true, // so we can split headers and body
		];

		// Handle request body
		if ($body !== null) {
			// If body is array or object, encode as JSON by default
			if (is_array($body) || is_object($body)) {
				$normalized = $this->normalizeForJson($body);
				$payload = json_encode($normalized, JSON_THROW_ON_ERROR);
			} else {
				$payload = (string)$body;
			}
			$options[CURLOPT_POSTFIELDS] = $payload;
		}

		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);

		if ($response === false) {
			$err = curl_error($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			throw new \Exception("cURL error ({$errno}): {$err}");
		}

		$statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		$headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$responseBody = substr($response, $headerSize) ?: '';
		curl_close($ch);

		$outStatusCode = $statusCode;

		if ($statusCode >= 400) {
			$message = $this->extractErrorMessage($responseBody) ?? 'HTTP error';
			throw new \Exception("HTTP {$statusCode}: {$message}");
		}

		if (StringHelper::isBlank($responseBody)) {
			return null;
		}

		try {
			return json_decode($responseBody, false, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $_) {
			// Fall through to return raw body if JSON invalid
		}

		return $responseBody;
	}

	public function get(string $endpoint, ?array $queryParams = []): mixed {
		return $this->request('GET', $endpoint, $queryParams, null);
	}

	public function post(string $endpoint, mixed $data): mixed {
		return $this->request('POST', $endpoint, [], $data);
	}

	public function put(string $endpoint, mixed $data): mixed {
		return $this->request('PUT', $endpoint, [], $data);
	}

	public function patch(string $endpoint, mixed $data): mixed {
		return $this->request('PATCH', $endpoint, [], $data);
	}

	public function delete(string $endpoint): void {
		$this->request('DELETE', $endpoint, [], null, $statusCode);
	}

}
