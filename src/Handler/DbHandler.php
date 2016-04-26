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
	/** @var */
	private $expireColumn;

	/**
	 * PhpArrayHandler constructor.
	 * @param PDO $pdo
	 * @param string $table
	 * @param string $keyColumn
	 * @param string $valueColumn
	 * @param $expireColumn
	 */
	public function __construct(PDO $pdo, $table, $keyColumn, $valueColumn, $expireColumn)
	{
		$this->pdo = $pdo;
		$this->table = $table;
		$this->keyColumn = $keyColumn;
		$this->valueColumn = $valueColumn;
		$this->expireColumn = $expireColumn;
	}


	public function set($key, $value, $expireSeconds = null)
	{
		$statement = $this->pdo->prepare('
		INSERT INTO 
			`'.  $this->table . '` 
		SET 
			`' . $this->keyColumn . '`=:key,
			`' . $this->valueColumn . '`=:value,		 
			`' . $this->expireColumn . '`=:expire 		 
		ON DUPLICATE KEY UPDATE `' . $this->valueColumn . '`=:value, `' . $this->expireColumn . '`=:expire
		;');

		$statement->execute([
			'key' => $key,
			'value' => $value,
			'expire' => $this->calcExpire($expireSeconds)
		]);

		$this->config[$key] = $value;
	}


	private function calcExpire($expireSeconds)
	{
		if (is_null($expireSeconds))
		{
			return null;
		}

		return (new \DateTime())
			->modify(sprintf('+%d SECONDS', $expireSeconds))
			->format('Y-m-d H:i:s');
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
		$this->deleteExpired();

		$statement = $this->pdo->prepare('
		SELECT
			`' . $this->keyColumn . '`, `' . $this->valueColumn . '`
		FROM
			`' . $this->table . '`
		;');
		$statement->execute();

		$this->config = [];
		while ($data = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$this->config[$data[$this->keyColumn]] = $data[$this->valueColumn];
		}

		$this->isLoaded = true;
	}


	private function deleteExpired()
	{
		$statement = $this->pdo->prepare('
		DELETE FROM
			`' . $this->table . '` 
		WHERE 
			`' . $this->expireColumn . '` IS NOT NULL AND `' . $this->expireColumn . '`<=:now
		;');
		$statement->execute([
			'now' => (new \DateTime())->format('Y-m-d H:i:s')
		]);

	}

}
