<?php
class LogOutPage extends Page {
	public function setup() {
		$this->user->logOut();
		redirectLocal();
	}
}
