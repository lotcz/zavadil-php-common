<?php

declare(strict_types=1);

namespace Zavadil\Common\Settings;

interface Settings {

    public function get(string $key = '', mixed $default = null): mixed;
}
