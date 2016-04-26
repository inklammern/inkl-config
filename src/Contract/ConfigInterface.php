<?php

namespace Inkl\Config\Contract;

interface ConfigInterface
{

	public function set($path, $value, $expireSeconds = null);

	public function get($path, $default = null);

}
