<?php
class Database {
	private $db_host = 'apex.ttu.ee';
	private $db_port = 7301;
	private $db_database = 'pbbans';
	private $db_user = 'pbbans_ruhmakontod';
	private $db_password = '3fm8k92sdfgw';
	
	private $dbh;
	
	private static $instance;
	
	public static function getInstance() {
		if (self::$instance === null) {
			new Database();
		}
		return self::$instance;
	}
	
	public function __construct() {
		$dbname = $this->db_database;
		$host = $this->db_host;
		$port = $this->db_port;
		try {
			$this->dbh = new PDO(
					"pgsql:dbname=$dbname;host=$host;port=$port", 
					$this->db_user, $this->db_password);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$instance = $this;
		} catch (PDOException $e) {
			ob_clean();
			die('Unable to establish DB connection: ' . $e->getMessage());
		}
	}
	
	public function getDatabaseHandle() {
		return $this->dbh;
	}
	
	public function closeConnection() {
		$this->dbh = null;
	}
}
