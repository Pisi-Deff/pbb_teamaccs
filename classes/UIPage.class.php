<?php
abstract class UIPage extends Page {
	public function getHeader() {
		return parent::getHeader() . $this->getHeaderUI();
	}
	
	public function getHeaderUI() {
		return <<<ENDCONTENT

<header>
	<span id="username">Kasutaja: {$this->session->getUsername()}</span>
	<nav id="headermenu">
		<a class="headerlink" href="index.php">Esileht</a>
		<a class="headerlink" href="index.php?employee=Index">Töötajapaneel</a>
		<a class="headerlink" href="index.php?page=LogOut">Logi välja</a>
	</nav>
</header>

ENDCONTENT;
	}
	
	public function getSideBar() {
		return '';
	}
	
	public function getContent() {
		$content = $this->getSideBar() . "\n";
		$content .= parent::getContent();
		return $content;
	}
}