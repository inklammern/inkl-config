<?php

namespace Inkl\Config;

use Inkl\Config\Contract\ConfigInterface;
use Inkl\Config\Contract\HandlerInterface;
use Kir\MySQL\Builder\Exception;

class Config implements ConfigInterface
{
	private $handlers = [];

	public function addHandler($namespace, HandlerInterface $handler)
	{
		$this->handlers[$namespace] = $handler;
	}


	public function set($path, $value, $expireSeconds = null)
	{
		$handler = $this->getHandler($path);

		return $handler->set($this->getKeyFromPath($path), $value, $expireSeconds);
	}


	public function get($path, $default = null)
	{
		$handler = $this->getHandler($path);

		return $handler->get($this->getKeyFromPath($path));
	}


	private function getHandler($path)
	{
		$namespace = $this->getNamespaceFromPath($path);

		if (!isset($this->handlers[$namespace]))
		{
			throw new Exception(sprintf('no handler for namespace "%s"', $namespace));
		}

		return $this->handlers[$namespace];
	}


	private function getNamespaceFromPath($path)
	{
		return current(explode('/', $path, 2));
	}


	private function getKeyFromPath($path)
	{
		return end(explode('/', $path, 2));
	}

}
