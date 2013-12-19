<?php
class TeamAccount {
	protected $id;
	
	public function __construct($id) {
		if ($id === null) {
			throw new Exception('Team Account cannot have an id of null');
		}
		$this->id = $id;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function db_getData() {
		$result = null;
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare(
					'SELECT * FROM Rühmakontode_nimekiri WHERE Rühmakonto_ID = ?');
			$stmt->execute(array($this->id));
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $result;
	}
	
	public function db_getUsers() {
		$users = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT * FROM Kasutajate_nimekiri ' .
					'WHERE Rühmakonto_ID = ?');
			$stmt->execute(array($this->getID()));
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$users[$row['kasutaja_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $users;
	}
	
	public function db_getServers() {
		$servers = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT * FROM Mänguserverite_nimekiri ' .
					'WHERE Rühmakonto_ID = ?');
			$stmt->execute(array($this->getID()));
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$servers[$row['mänguserver_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $servers;
	}
	
	public function db_getComments() {
		$comments = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('SELECT * FROM Rühmakonto_kommentaaride_nimekiri ' .
					'WHERE Rühmakonto_ID = ?');
			$stmt->execute(array($this->getID()));
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$comments[] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $comments;
	}
	
	public function db_changeStatus($newStatus) {
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->prepare('UPDATE Rühmakonto SET Rühmakonto_staatus_ID = ? ' .
					'WHERE Rühmakonto_ID = ?');
			$stmt->execute(array($newStatus, $this->getID()));
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
	}
	
	public function addUser($user) {
		return $user->db_addToTeamAccount($this);
	}
	
	public function removeUser($user) {
		$user->db_removeFromTeamAccount($this);
	}
	
	public function createServer($ip, $port, $game) {
		return Server::db_createNew($this, $ip, $port, $game);
	}
	
	public function deleteServer($server) {
		$server->db_delete($this);
	}
	
	public static function createNew($name, $website, $email, $status,
			$applicationID = null) {
		$id = self::db_create($name, $website, $email, $status, $applicationID);
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
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $result;
	}
	
	public static function db_getList() {
		$teamAccounts = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query('SELECT * FROM Rühmakontode_nimekiri');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$teamAccounts[$row['rühmakonto_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $teamAccounts;
	}
	
	public static function db_getStatuses() {
		$statuses = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query('SELECT * FROM rühmakonto_staatus');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$statuses[$row['rühmakonto_staatus_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $statuses;
	}
}
