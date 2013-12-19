<?php
class Server {
	private $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function db_confirm() {
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT f_kinnita_mänguserver (?)');
			$stmt->execute(array($this->id));
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
	}
	
	public function db_delete($teamAccount = null) {
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$sql = 'DELETE FROM Mänguserver WHERE Mänguserver_ID = ?';
			if ($teamAccount !== null) {
				$sql .= ' AND Rühmakonto_ID = ?';
			}
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1, $this->getID());
			if ($teamAccount !== null) {
				$stmt->bindValue(2, $teamAccount->getID());
			}
			$stmt->execute();
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
	}
	
	public static function db_getListNew() {
		$servers = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query(
					'SELECT * FROM Uute_mänguserverite_nimekiri');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$servers[$row['mänguserver_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $servers;
	}
}
