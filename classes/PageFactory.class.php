<?php
class PageFactory {
	private $session;
	
	public function __construct($session) {
		$this->session = $session;
	}
	public function getPage($get, $post) {
		$page = null;
		$pageName = null;
		if (!empty($get['page'])) { 
			$pageName = $get['page'] . 'Page';
		} else if (!empty($get['employee'])) {
			$pageName = $get['employee'] . 'EmployeePage';
		}
		if ($pageName !== null && class_exists($pageName)
				&& (new ReflectionClass($pageName))->isInstantiable()) {
			$page = new $pageName($this->session, $get, $post);
		}
		if ($page === null) {
			$page = new IndexPage($this->session, $get, $post);
		}
		return $page;
	}
}
