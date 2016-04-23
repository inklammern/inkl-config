<?php

namespace Inkl\Config\Contract;

interface ConfigInterface
{

	public function set($path, $value);

	public function get($path, $default = null);

}
