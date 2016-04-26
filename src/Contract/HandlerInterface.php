<?php

namespace Inkl\Config\Contract;

interface HandlerInterface
{

	public function set($key, $value, $expireSeconds = null);

	public function get($key, $default = null);

}
