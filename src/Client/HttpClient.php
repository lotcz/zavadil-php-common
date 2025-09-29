<?php

declare(strict_types=1);

namespace Zavadil\Common\Client;

use Zavadil\Common\Helpers\JsonHelper;
use Zavadil\Common\Helpers\StringHelper;
use Zavadil\Common\Helpers\UrlHelper;

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

	private function buildUrl(string $endpoint, ?array $queryParams = null): string {
		return UrlHelper::of($this->baseUrl, $endpoint, $queryParams);
	}

	private function request(
		string $method,
		string $endpoint,
		?array $queryParams = null,
		mixed $body = null,
		?string $className = null,
		?int &$outStatusCode = null
	) {
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

		if ($body !== null) {
			$options[CURLOPT_POSTFIELDS] = JsonHelper::encode($body);
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

		return JsonHelper::decode($responseBody, $className);
	}

	public function get(string $endpoint, ?array $queryParams = [], ?string $className = null): mixed {
		return $this->request('GET', $endpoint, $queryParams, null, $className);
	}

	public function post(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed {
		return $this->request('POST', $endpoint, $queryParams, $data, $className);
	}

	public function put(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed {
		return $this->request('PUT', $endpoint, $queryParams, $data, $className);
	}

	public function patch(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed {
		return $this->request('PATCH', $endpoint, $queryParams, $data, $className);
	}

	public function delete(string $endpoint, ?array $queryParams = []): void {
		$this->request('DELETE', $endpoint, $queryParams, null, null);
	}

}
