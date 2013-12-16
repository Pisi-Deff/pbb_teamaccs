<?php
require_once 'functs/util.functs.php';

spl_autoload_register(function ($class) {
	$filename = 'classes/' . $class . '.class.php';
	if (file_exists($filename)) {
		include $filename;
	}
});
spl_autoload_register(function ($class) {
	$filename = 'pages/' . $class . '.class.php';
	if (file_exists($filename)) {
		include $filename;
	}
});
spl_autoload_register(function ($class) {
	$filename = 'employeepages/' . $class . '.class.php';
	if (file_exists($filename)) {
		include $filename;
	}
});

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

ob_start();
session_start();

$get = clean($_GET);
$post = clean($_POST);

$db = new Database();
$dbh = $db->getDatabaseHandle();
$user = new User($dbh);
$pageFactory = new PageFactory($dbh, $user);

if (!$user->isLoggedIn()) {
	$get['page'] = 'login';
}
$page = $pageFactory->getPage($get, $post);
echo $page->getPage();