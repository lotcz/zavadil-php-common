<?php

declare(strict_types=1);

namespace Zavadil\Common\Client;

/**
 * Generic interface for REST client operations
 *
 * @template T
 */
interface RestClient {
	/**
	 * Retrieve a resource by its endpoint
	 * @param string $endpoint The endpoint URL
	 * @param array<string,mixed> $queryParams Optional query parameters
	 * @return T
	 * @throws \Exception If the request fails
	 */
	public function get(string $endpoint, array $queryParams = []);

	/**
	 * Create a new resource
	 * @param string $endpoint The endpoint URL
	 * @param T $data The data to create
	 * @return T The created resource
	 * @throws \Exception If the request fails
	 */
	public function post(string $endpoint, $data);

	/**
	 * Update an existing resource
	 * @param string $endpoint The endpoint URL
	 * @param T $data The data to update
	 * @return T The updated resource
	 * @throws \Exception If the request fails
	 */
	public function put(string $endpoint, $data);

	/**
	 * Partially update an existing resource
	 * @param string $endpoint The endpoint URL
	 * @param array<string,mixed> $data The data to patch
	 * @return T The updated resource
	 * @throws \Exception If the request fails
	 */
	public function patch(string $endpoint, array $data);

	/**
	 * Delete a resource
	 * @param string $endpoint The endpoint URL
	 * @return bool True if deletion was successful
	 * @throws \Exception If the request fails
	 */
	public function delete(string $endpoint): bool;
}
