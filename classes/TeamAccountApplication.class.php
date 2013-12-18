<?php
class TeamAccountApplication {
	public static function db_getList() {
		$applications = array();
		try {
			$dbh = Database::getInstance()->getDatabaseHandle();
			$stmt = $dbh->query(
					'SELECT * FROM Uute_vastuvõetud_rühmakonto_avalduste_nimekiri');
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$applications[$row['rühmakonto_avaldus_id']] = $row;
			}
		} catch (PDOException $e) {
			Page::addMessage(new Message($e->getMessage(), 'error'));
		}
		return $applications;
	}
}
