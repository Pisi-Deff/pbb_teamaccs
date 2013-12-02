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
		<a class="headerlink" href="index.php?employee=index">Töötajate paneel</a>
		<a class="headerlink" href="index.php?page=logout">Logi välja</a>
	</nav>
</header>

ENDCONTENT;
	}
}
