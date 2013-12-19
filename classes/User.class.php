<?php
class User {
	private $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function db_removeFromTeamAccount($teamAccount) {
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT f_kustuta_kasutaja_rühmakontolt (?, ?)');
			$stmt->execute(array($teamAccount->getID(), $this->id));
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
	}
	
	public function db_addToTeamAccount($teamAccount) {
		$success = false;
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT f_lisa_rühmakontole_kasutaja (?, ?)');
			$success = $stmt->execute(array($teamAccount->getID(), $this->id));
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $success;
	}
	
	public static function db_listTeamAccountless() {
		$users = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query(
					'SELECT * FROM Kasutajate_nimekiri WHERE Rühmakonto_ID IS NULL');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$users[$row['kasutaja_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $users;
	}
}
