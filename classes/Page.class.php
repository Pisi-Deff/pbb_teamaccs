<?php
abstract class Page {
	protected $baseTitle = 'PBBans';
	protected $title = '';
	protected $content = '';
	protected $messages = array();
	
	protected $user;
	
	protected $get;
	protected $post;
	
	public function __construct($user, $get, $post) {
		$this->user = $user;
		$this->get = $get;
		$this->post = $post;
		$this->setup();
	}
	
	abstract public function setup();
	
	public function getHTMLHeader() {
		return <<<ENDCONTENT
<!DOCTYPE html>
<html>
<head>

<title>{$this->getTitle()}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/pbbans.css" />

</head>
<body>

ENDCONTENT;
	}
	
	public function getTitle() {
		$title = $this->baseTitle;
		if (!empty($this->title)) {
			$title = $this->title . ' - ' . $title;
		}
		return $title;
	}
	
	protected function setTitle($title) {
		$this->title = $title;
	}
	
	public function getMessages() {
		$result = '';
		if (!empty($this->messages)) {
			foreach ($this->messages as $msg) {
				$result .= $msg->toHTML();
			}
		}
		return $result;
	}
	
	public function getHeader() {
		return '';
	}
	
	public function addMessage($msg) {
		$this->messages[] = $msg;
	}
	
	public function getSideBar() {
		return '';
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getFooter() {
		return '';
	}
	
	public function getHTMLFooter() {
		return <<<ENDCONTENT

</body>
</html>
ENDCONTENT;
	}
	
	public function getPage() {
		$page = $this->getHTMLHeader() . "\n";
		$page .= $this->getHeader() . "\n";
		$page .= $this->getSideBar() . "\n";
		if (!empty($this->title)) {
			$page .= '<h1>' . $this->title . '</h1>';
		}
		$page .= $this->getMessages() . "\n";
		$page .= $this->getContent() . "\n";
		$page .= $this->getFooter() . "\n";
		$page .= $this->getHTMLFooter();
		return $page;
	}
}
