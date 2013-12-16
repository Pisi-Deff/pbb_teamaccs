<?php
class Database {
	private $db_host = 'apex.ttu.ee';
	private $db_port = 7301;
	private $db_database = 'pbbans';
	private $db_user = 'pbbans_ruhmakontod';
	private $db_password = '3fm8k92sdfgw';
	
	private $dbh;
	
	public function __construct() {
		$dbname = $this->db_database;
		$host = $this->db_host;
		$port = $this->db_port;
		try {
			$this->dbh = new PDO(
					"pgsql:dbname=$dbname;host=$host;port=$port", 
					$this->db_user, $this->db_password);
		} catch (PDOException $e) {
			ob_clean();
			die('Unable to establish DB connection: ' . $e->getMessage());
		}
	}
	
	public function getDatabaseHandle() {
		return $this->dbh;
	}
}
