<?php
class User {
	protected $dbh;
	
	private $jobs = null;
	
	public function __construct($dbh) {
		$this->dbh = $dbh;
	}
	
	public function isLoggedIn() {
		return isset($_SESSION['pbb_userID']);
	}
	
	public function tryLogIn($username, $password) {
		$userID = $this->dbh->user_tryLogIn($username, $password);
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
			$this->jobs = $this->dbh->user_getJobs($_SESSION['pbb_userID']);
		}
		return $this->jobs;
	}
}
