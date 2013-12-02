<?php
class LoginPage extends Page {
	public function setup() {
		$cleanPost = clean($_POST);
		if (isset($cleanPost['username']) && isset($cleanPost['password'])) {
			$loginSuccess = false;
			if (empty($cleanPost['username']) || empty($cleanPost['password'])) {
				$this->addMessage(
						new Message('Kasutajanimi või parool sisestamata.', 'error'));
			} else {
				$loginSuccess = $this->user->tryLogIn(
						$cleanPost['username'], $cleanPost['password']);
				if ($loginSuccess) {
					redirectLocal();
				} else {
					$this->addMessage(
							new Message('Sellise kasutajanime ja parooliga kasutajat ei eksisteeri.', 'error'));
				}
			}
			if (!$loginSuccess) {
				$this->user->logOut();
			}
		}
		$this->content .= <<<ENDCONTENT
<div>
	<form id="loginform" class="content" action="index.php?page=login" method="post">
		<fieldset>
			Kasutajanimi:<br />
			<input type="text" name="username" /><br />
			Parool:<br />
			<input type="password" name="password" /><br />
			<input class="button" type="submit" name="login" value="Logi sisse" />
		</fieldset>
	</form>
</div>
ENDCONTENT;
	}
}
