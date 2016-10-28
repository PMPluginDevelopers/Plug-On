<?php
namespace plugon\utils;
class MySQLHelper {
	
	const GET_USER = "SELECT * FROM users WHERE name LIKE '?'";

    /**
     * @var \mysqli
     */
	protected $mysqli;
    /**
     * @var string
     */
    public $error;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int $port
     */
	public function __construct($host, $user, $password,  $database, $port = 3306) {
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
	public function insert($table, array $col, array $values, $condition = ""){
		$query = "INSERT INTO $table (";
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
	public function query($query) {
		global $log;
		$r = $this->mysqli->query($query);
		if(!$r) {
            $this->error = mysqli_error($this->mysqli);
            $log->error("================================================");
			$log->error("MySQL Query failed (" . $this->mysqli->errno . "): " . $this->mysqli->error);
			$log->error("Query: '$query'");
			$log->error("================================================");
			return false;
		}
		return $r;
	}
	
	public function getResource(){
		return $this->mysqli;
	}

	/**
	 * @param $query
	 * @param array $params
	 * @return \mysqli_stmt
	 */
	public function prepare($query, array $params = []) {
		$stmt = $this->mysqli->prepare($query);
		foreach($params as $param) {
			$stmt->bind_param(self::typeToCharacter($param), $param);
		}
		return $stmt;
	}

	/**
	 * @param $var
	 * @return string
	 */
	public static function typeToCharacter($var){
		if(is_int($var)) {
			return "i";
		}elseif(is_double($var)) {
			return "d";
		}
		return "s";
	}
	
}
