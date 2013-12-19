<?php
class LogOutPage extends Page {
	public function setup() {
		$this->session->logOut();
		redirectLocal();
	}
}
