<?php
class LogoutPage extends Page {
	public function setup() {
		$this->user->logOut();
		redirectLocal();
	}
}
