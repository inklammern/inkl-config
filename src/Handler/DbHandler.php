<?php

namespace Inkl\Config\Handler;

use Inkl\Config\Contract\HandlerInterface;
use Kir\MySQL\Database;
use PDO;

class DbHandler implements HandlerInterface
{
	/**
	 * @var PDO
	 */
	private $pdo;
	/**
	 * @var string
	 */
	private $table;
	/**
	 * @var string
	 */
	private $keyColumn;
	/**
	 * @var string
	 */
	private $valueColumn;

	private $isLoaded = false;

	private $config = [];

	/**
	 * PhpArrayHandler constructor.
	 * @param PDO $pdo
	 * @param string $table
	 * @param string $keyColumn
	 * @param string $valueColumn
	 */
	public function __construct(PDO $pdo, $table, $keyColumn, $valueColumn)
	{
		$this->pdo = $pdo;
		$this->table = $table;
		$this->keyColumn = $keyColumn;
		$this->valueColumn = $valueColumn;
	}


	public function set($key, $value)
	{
		$statement = $this->pdo->prepare('
		INSERT INTO 
			`'.  $this->table . '` 
		SET 
			`' . $this->keyColumn . '`=:key,
			`' . $this->valueColumn . '`=:value 			 
		ON DUPLICATE KEY UPDATE `' . $this->valueColumn . '`=:value
		;');

		$statement->execute([
			'key' => $key,
			'value' => $value
		]);

		$this->config[$key] = $value;
	}


	public function get($key, $default = null)
	{
		if (!$this->isLoaded)
		{
			$this->load();
		}

		if (array_key_exists($key, $this->config))
		{
			return $this->config[$key];
		}

		return $default;
	}


	private function load()
	{
		$statement = $this->pdo->prepare('
		SELECT
			`' . $this->keyColumn . '`, `' . $this->valueColumn . '`
		FROM
			`' . $this->table . '`;
		;');
		$statement->execute();

		$this->config = [];
		while ($data = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$this->config[$data[$this->keyColumn]] = $data[$this->valueColumn];
		}

		$this->isLoaded = true;
	}

}
