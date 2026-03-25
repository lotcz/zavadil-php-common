<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\Imagez;

use Zavadil\Common\Client\HttpClient;
use Zavadil\Common\Client\Imagez\Payload\ImageHealthPayload;
use Zavadil\Common\Helpers\ImagezHelper;
use Zavadil\Common\Helpers\JsonHelper;

class ImagezHttpClient extends HttpClient {

	private string $secretToken;

	public function __construct(string $imagezUrl, string $secretToken) {
		parent::__construct($imagezUrl);
		$this->secretToken = $secretToken;
	}

	public function getOriginalImageUrl(string $name): string {
		return ImagezHelper::getOriginalImageUrl($this->baseUrl, $name);
	}

	public function getResizedImageUrl(
		string $name,
		int $width,
		int $height,
		string $type = "fit",
		?string $ext = null,
		?string $verticalAlign = null,
		?string $horizontalAlign = null,
		?int $page = null
	): string {
		return ImagezHelper::getResizedImageUrl(
			$this->baseUrl,
			$this->secretToken,
			$name,
			$width,
			$height,
			$type,
			$ext,
			$verticalAlign,
			$horizontalAlign,
			$page
		);
	}

	public function uploadFile(string $file): ImageHealthPayload {
		$ch = curl_init();
		if ($ch === false) {
			throw new \Exception('Failed to initialize cURL');
		}

		$url = $this->buildUrl('images/upload', ['token' => $this->secretToken]);
		$headers = $this->prepareHeaders(['Accept: application/json, */*;q=0.8']);

		$options = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => $this->timeout,
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HEADER => true,
		];

		$options[CURLOPT_POSTFIELDS] = [
			'image' => new \CURLFile(
				$file,
				'application/octet-stream',
				basename($file)
			)
		];

		$options[CURLOPT_HTTPHEADER] = array_values($headers);
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

		if ($statusCode >= 400) {
			$message = $this->extractErrorMessage($responseBody) ?? 'HTTP error';
			throw new \Exception("HTTP {$statusCode}: {$message}");
		}

		return JsonHelper::decode($responseBody, ImageHealthPayload::class);
	}

}
