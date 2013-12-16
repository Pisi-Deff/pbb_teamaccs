<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once 'functs/util.functs.php';
require_once 'functs/form.functs.php';

spl_autoload_register(function ($class) {
	$filenames = array('classes/' . $class . '.class.php',
			'pages/' . $class . '.class.php',
			'pages/employee/' . $class . '.class.php'
		);
	foreach ($filenames as $filename){
		if (file_exists($filename)) {
			include $filename;
			break;
		}
	}
});

ob_start();
session_start();

$get = clean($_GET);
$post = clean($_POST);

$db = Database::getInstance();
$user = new User();
$pageFactory = new PageFactory($user);

if (!$user->isLoggedIn()) {
	$get['page'] = 'LogIn';
}
$page = $pageFactory->getPage($get, $post);
echo $page->getPage();

$db->closeConnection();