<?php
class Game {
	public static function db_getGames() {
		$games = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query('SELECT * FROM Mäng');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$games[$row['mängu_lühinimi']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->errorInfo[2], 'error'));
		}
		return $games;
	}
}
