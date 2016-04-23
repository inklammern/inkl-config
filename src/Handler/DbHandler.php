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
			'.  $this->table . ' 
		SET 
			' . $this->valueColumn . '=:value 
		WHERE 
		' . $this->keyColumn = ':key 
		ON DUPLICATE KEY ' . $this->valueColumn . '=:value
		;');

		return $statement->execute([
			'key' => $key,
			'value' => $value
		]);
	}


	public function get($key, $default = null)
	{

		$statement = $this->pdo->prepare('
		SELECT 
			' . $this->valueColumn . ' 
		FROM 
			' . $this->table . ' 
		WHERE 
			' . $this->keyColumn . '=:key
		;');

		if ($column =$statement->fetchColumn())
		{
			return $column;
		}

		return $default;
	}

}
