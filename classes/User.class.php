<?php
class User {
	private $jobs = null;
	
	public function isLoggedIn() {
		return isset($_SESSION['pbb_userID']);
	}
	
	public function tryLogIn($username, $password) {
		$userID = $this->db_tryLogIn($username, $password);
		if ($userID !== null) {
			$_SESSION['pbb_userID'] = $userID;
			$_SESSION['pbb_username'] = $username;
			return true;
		}
		return false;
	}
	
	public function logOut() {
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
	}
	
	public function getUsername() {
		if (!empty($_SESSION['pbb_username'])) {
			return $_SESSION['pbb_username'];
		} else {
			return 'ei ole sisse logitud';
		}
	}
	
	public function isEmployed() {
		$jobs = $this->getJobs();
		return !empty($jobs);
	}
	
	public function getJobs() {
		if ($this->isLoggedIn() && $this->jobs === null) {
			$this->jobs = $this->db_getJobs($_SESSION['pbb_userID']);
		}
		return $this->jobs;
	}
	
	public function db_tryLogIn($username, $password) {
		$result = null;
		try {
			$stmt = Database::getInstance()->getDatabaseHandle()->prepare(
					"SELECT f_logi_sisse (?, ?)");
			$stmt->execute(array($username, $password));
			$result = $stmt->fetch(PDO::FETCH_NUM)[0];
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $result;
	}
	
	public function db_getJobs($userID) {
		$jobs = array();
		try {
			$stmt = Database::getInstance()->getDatabaseHandle()->prepare(
					"SELECT * FROM f_leia_kasutaja_ametid (?)");
			$stmt->execute(array($userID));
			$queryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($queryResult as $job) {
				$jobs[$job['amet_id']] = $job['nimetus'];
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $jobs;
	}
}
