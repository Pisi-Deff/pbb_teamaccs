<?php
class TeamAccountApplication {
	private $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function getData() {
		$data = null;
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare(
					'SELECT * FROM Uute_vastuvõetud_rühmakonto_avalduste_nimekiri ' .
					'WHERE Rühmakonto_avaldus_ID = ?');
			$stmt->execute(array($this->getID()));
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $data;
	}
	
	public static function db_getList() {
		$applications = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query(
					'SELECT * FROM Uute_vastuvõetud_rühmakonto_avalduste_nimekiri');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$applications[$row['rühmakonto_avaldus_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $applications;
	}
}
