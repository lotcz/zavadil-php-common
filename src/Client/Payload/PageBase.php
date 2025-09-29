<?php

declare(strict_types=1);

namespace Zavadil\Common\Client\Payload;

use Zavadil\Common\Helpers\JsonHelper;

abstract class PageBase extends PayloadBase {

	public int $totalItems;

	public int $pageSize;

	public int $pageNumber;

	public array $content = [];

	public abstract function getContentClass(): string;

	public function hydrateData(mixed $data) {
		$this->content = JsonHelper::hydrateToClass($this->getContentClass(), $data->content);
	}

}
