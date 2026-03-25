<?php

namespace Zavadil\Common\Client\Imagez\Payload;

use Zavadil\Common\Client\Payload\PayloadBase;

class ImageHealthPayload extends PayloadBase {

	public string $name;

	public int $size;

	public int $width;

	public int $height;

	public string $mime;

	public static function of(
		string $name,
		int $size,
		int $width,
		int $height,
		string $mime
	): ImageHealthPayload {
		$obj = new self();
		$obj->name = $name;
		$obj->size = $size;
		$obj->width = $width;
		$obj->height = $height;
		$obj->mime = $mime;
		return $obj;
	}

}
