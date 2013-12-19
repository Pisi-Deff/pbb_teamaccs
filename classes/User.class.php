<?php
class User {
	private $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function db_removeUserFromTeamAccount($teamAccount) {
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT f_kustuta_kasutaja_rühmakontolt (?, ?)');
			$stmt->execute(array($teamAccount->getID(), $this->id));
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
	}
}
