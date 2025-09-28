<?php

namespace Zavadil\Common\Messages;

class UserMessage {

	public string $text;

	public string $type = 'info';

	public function __construct(string $text, string $type = 'info') {
		$this->text = $text;
		$this->type = $type;
	}

}
