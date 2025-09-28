<?php

declare(strict_types=1);

namespace Zavadil\Common\Client;

/**
 * Generic interface for REST client operations
 */
interface RestClient {

	public function get(string $endpoint, ?array $queryParams = []): mixed;

	public function post(string $endpoint, mixed $data): mixed;

	public function put(string $endpoint, mixed $data): mixed;

	public function patch(string $endpoint, mixed $data): mixed;

	public function delete(string $endpoint): void;

}
