<?php
class LogInPage extends Page {
	public function setup() {
		$this->setTitle('Logi sisse');
		if (isset($this->post['username']) && isset($this->post['password'])) {
			$loginSuccess = false;
			if (empty($this->post['username']) || empty($this->post['password'])) {
				self::addMessage(new Message(
						'Kasutajanimi või parool sisestamata.', 'error'));
			} else {
				$loginSuccess = $this->session->tryLogIn(
						$this->post['username'], $this->post['password']);
				if ($loginSuccess) {
					redirectLocal();
				} else {
					self::addMessage(
							new Message('Sellise kasutajanime ja parooliga kasutajat ei eksisteeri.', 'error'));
				}
			}
			if (!$loginSuccess) {
				$this->session->logOut();
			}
		}
		$this->content .= $this->getLoginForm();
	}
	
	public function getLoginForm() {
		return <<<ENDCONTENT
<div>
	<form id="loginform" class="content" action="index.php?page=LogIn" method="post">
		<fieldset>
			Kasutajanimi:<br />
			<input type="text" name="username" /><br />
			Parool:<br />
			<input type="password" name="password" /><br />
			<br />
			<input class="button" type="submit" name="login" value="Logi sisse" />
		</fieldset>
	</form>
</div>
ENDCONTENT;
	}
}
