<?php

declare(strict_types=1);

namespace Zavadil\Common\Client;

/**
 * Generic interface for REST client operations
 */
interface RestClient {

	public function get(string $endpoint, ?array $queryParams = [], ?string $className = null): mixed;

	public function post(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed;

	public function put(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed;

	public function patch(string $endpoint, mixed $data, ?array $queryParams = [], ?string $className = null): mixed;

	public function delete(string $endpoint, ?array $queryParams = []): void;

}
