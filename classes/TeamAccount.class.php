<?php
class TeamAccount {
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public static function createNew() {
		return self::db_create();
	}
	
	private static function db_create() {
		$dbh = Database::getInstance()->getDatabaseHandle();
	}
	
	public static function db_getStatuses() {
		$dbh = Database::getInstance()->getDatabaseHandle();
		$stmt = $dbh->query("SELECT * FROM rühmakonto_staatus");
		$statuses = array();
		while ($row = $stmt->fetch()) {
			$statuses[$row['rühmakonto_staatus_id']] = $row;
		}
		return $statuses;
	}
}
