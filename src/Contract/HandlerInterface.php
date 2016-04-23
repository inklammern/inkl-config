<?php

namespace Inkl\Config\Contract;

interface HandlerInterface
{

	public function set($key, $value);

	public function get($key, $default = null);

}
