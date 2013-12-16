<?php
class PageFactory {
	private $user;
	
	public function __construct($user) {
		$this->user = $user;
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
			$page = new $pageName($this->user, $get, $post);
		}
		if ($page === null) {
			$page = new IndexPage($this->user, $get, $post);
		}
		return $page;
	}
}
