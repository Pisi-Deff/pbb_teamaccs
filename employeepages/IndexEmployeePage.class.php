<?php

class IndexEmployeePage extends EmployeePage {
	public function setup() {
		if (parent::setup()) {
			$this->content .= 
					'<div class="content">Olete sisenenud töötajate paneeli.</div>';
		}
	}
}
