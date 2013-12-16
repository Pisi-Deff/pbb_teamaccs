<?php
abstract class UIPage extends Page {
	public function getHeader() {
		return parent::getHeader() . $this->getHeaderUI();
	}
	
	public function getHeaderUI() {
		return <<<ENDCONTENT

<header>
	<span>Kasutaja: {$this->user->getUsername()}</span>
	<nav id="headermenu">
		<a class="headerlink" href="index.php">Esileht</a>
		<a class="headerlink" href="index.php?employee=Index">Töötajapaneel</a>
		<a class="headerlink" href="index.php?page=LogOut">Logi välja</a>
	</nav>
</header>

ENDCONTENT;
	}
}
