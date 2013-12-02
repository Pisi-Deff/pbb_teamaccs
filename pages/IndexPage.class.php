<?php
class IndexPage extends UIPage {
	public function setup() {
		$this->content = <<<ENDCONTENT
<div class="content">
	hello world.
</div>
ENDCONTENT;
	}
}
