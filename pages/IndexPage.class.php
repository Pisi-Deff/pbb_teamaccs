<?php
class IndexPage extends UIPage {
	public function setup() {
		$this->content = <<<ENDCONTENT
<div class="content">
	Teretulemast PBBans-i lehele.<br />
	<br />
	Hetkel on realiseeritud ainult rühmakontode haldaja roll.<br />
	Selle kasutamiseks sisenege ülal töötajatepaneeli.
</div>
ENDCONTENT;
	}
}
