<?php
class TeamAccount {
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public static function createNew($name, $website, $email, $status,
			$applicationID = null) {
		$id = self::db_create($name, $website, $email, $status, $applicationID);
		var_dump($id);
		if ($id !== null) {
			return new TeamAccount($id);
		}
		return null;
	}
	
	private static function db_create($name, $website, $email, $status,
			$applicationID) {
		$result = null;
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT f_loo_rühmakonto (?, ?, ?, ?, ?)');
			$stmt->execute(array($name, $website, $email, $status, $applicationID));
			$result = $stmt->fetch(PDO::FETCH_NUM)[0];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		return $result;
	}
	
	public static function db_getStatuses() {
		$statuses = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query('SELECT * FROM rühmakonto_staatus');
			while ($row = $stmt->fetch()) {
				$statuses[$row['rühmakonto_staatus_id']] = $row;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		return $statuses;
	}
}
