<?php
namespace plugon\utils;
use plugon\Plugon;

class MySQLHelper {
	
	const GET_USER = "SELECT * FROM `users` WHERE `name` LIKE '?'";
	
	protected $mysqli;
	
	public function __construct(string $host, string $user, string $password, string $database, int $port = 3306) {
		$this->mysqli = new \mysqli($host, $user, $password, $database, $port);
		if($this->mysqli->connect_errno) {
			throw new \RuntimeException("Connection to MySQL database failed (" . $this->mysqli->connect_errno . "): " . $this->mysqli->connect_error);
		}
	}
	
	/**
	 * @param string $table
	 * @param array $col columns
	 * @param array $values
	 * @param string $condition
	 * 
	 * @return int affected rows
	 */
	public function insert(string $table, array $col, array $values, string $condition = "") : int {
		$query = "INSERT INTO `$table` (";
		foreach ($col as $key => $value){
			$query .= "$value, ";
		}
		$query = rtrim($query, ", ");
		$query .= ") VALUES (";
		
		foreach($values as $val) {
			if(is_bool($val)) {
				$val = $val ? 1 : 0;
			} elseif(is_array($val) or is_object($val)) {
				$val = serialize($val);
			}
			$query .= $val.", ";
		}
		$query = rtrim($query, ", ");
		$query .= ")".($condition !== "" ? $condition : " ".$condition).";";

		$this->query($query);
		return (int) $this->mysqli->affected_rows;
	}
	
	/**
	 * @param string $query
	 * @return mixed
	 */
	public function query(string $query) {
		global $log;
		$r = $this->mysqli->query($query);
		if(!$r) {
			$log->e("MySQL Query failed (" . $this->mysqli->errno . "): " . $this->mysqli->error);
			$log->e("Query: '$query'");
			$log->e("================================================");
			return false;
		}
		return $r;
	}
	
	public function getResource() : \mysqli {
		return $this->mysqli;
	}
	
	public function prepare($query, array $params = []) {
		$stmt = $this->mysqli->prepare($query);
		foreach($param as $param) {
			$stmt->bindParam(self::typeToCharacter($param), $param);
		}
		return $stmt;
	}
	
	public static function typeToCharacter($var) : string {
		if(is_int($var)) {
			return "i";
		}elseif(is_double($var)) {
			return "d";
		}
		return "s";
	}
	
}
