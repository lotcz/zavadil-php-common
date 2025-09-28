<?php

namespace Zavadil\Common\Client\Payload;

use Zavadil\Common\Helpers\JsonHelper;

abstract class PageBase extends PayloadBase {

	public int $totalItems;

	public int $pageSize;

	public int $pageNumber;

	public array $content = [];

	public abstract function getContenType(): string;

	public function setData(mixed $data) {
		parent::setData($data);
		$this->content = JsonHelper::hydrateToClass($this->getContenType(), $data->content);
	}

}
