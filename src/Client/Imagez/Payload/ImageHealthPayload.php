<?php

namespace Zavadil\Common\Client\Imagez\Payload;

use Zavadil\Common\Client\Payload\PayloadBase;

class ImageHealthPayload extends PayloadBase {

	public string $name;

	public int $size;

	public int $width;

	public int $height;

	public string $mime;

	public function __construct(
		string $name,
		int $size,
		int $width,
		int $height,
		string $mime
	) {
		$this->name = $name;
		$this->size = $size;
		$this->width = $width;
		$this->height = $height;
		$this->mime = $mime;
	}

}
