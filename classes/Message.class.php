<?php
class Message {
	protected $text;
	protected $class;
	
	public function __construct($text, $class = 'notice') {
		$this->text = $text;
		$this->class = $class;
	}
	
	public function toHTML() {
		return '<div class="message ' . $this->class . '">' . $this->text . '</div>';
	}
}
