<?php

namespace Zavadil\Common\Messages;

class UserMessages {

	public array $messages = [];

	public function addMessage(UserMessage $message): void {
		$this->messages[] = $message;
	}

	public function add(string $text, string $type = 'info'): void {
		$this->addMessage(new UserMessage($text, $type));
	}

	public function info(string $text): void {
		$this->add($text, 'info');
	}

	public function error(string $text): void {
		$this->add($text, 'error');
	}

	public function warning(string $text): void {
		$this->add($text, 'warning');
	}

	public function success(string $text): void {
		$this->add($text, 'success');
	}
}
