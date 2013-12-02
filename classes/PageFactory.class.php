<?php
class PageFactory {
	private $dbh;
	private $user;
	public function __construct($dbh, $user) {
		$this->dbh = $dbh;
		$this->user = $user;
	}
	public function getPage($get, $post) {
		$page = null;
		$pageName = null;
		if (!empty($get['page'])) { 
			$pageName = ucfirst(strtolower($get['page'])) . 'Page';
		} else if (!empty($get['employee'])) {
			$pageName = ucfirst(strtolower($get['employee'])) . 'EmployeePage';
		}
		if ($pageName !== null && class_exists($pageName)
				&& (new ReflectionClass($pageName))->isInstantiable()) {
			$page = new $pageName($this->dbh, $this->user);
		}
		if ($page === null) {
			$page = new IndexPage($this->dbh, $this->user);
		}
		return $page;
	}
}
