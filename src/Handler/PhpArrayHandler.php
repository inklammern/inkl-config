<?php

namespace Inkl\Config\Handler;

use Inkl\Config\Contract\HandlerInterface;

class PhpArrayHandler implements HandlerInterface
{
	/**
	 * @var array
	 */
	private $config;


	/**
	 * PhpArrayHandler constructor.
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function set($key, $value, $expireSeconds = null)
	{
		throw new \Exception(sprintf('keys handled by php arrays are not writeable'));
	}


	public function get($key, $default = null)
	{
		$current = &$this->config;
		foreach (explode('/', $key) as $keyPart) {

			if (!isset($current[$keyPart])) return $default;
			$current = &$current[$keyPart];
		}

		return $current;
	}

}
