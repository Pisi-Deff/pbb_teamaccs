<?php
class NewServersEmployeePage extends EmployeePage {
	public function setupEmployee() {
		$this->setTitle('Uued mänguserverid');
		if (!empty($this->post['serverid']) && is_numeric($this->post['serverid']) &&
				($id = intval($this->post['serverid'])) > 0) {
			$server = new Server($id);
			if (!empty($this->post['confirmserver'])) {
				$server->db_confirm();
			} else if (!empty($this->post['denyserver'])) {
				$server->db_delete();
			}
		}
		$this->content .= $this->genNewServersTable();
	}
	
	public function genNewServersTable() {
		$table = <<<ENDCONTENT
<table>
	<tr class="toprow">
		<td>IP:port</td>
		<td>Mäng</td>
		<td>Rühm</td>
		<td></td>
	</tr>
ENDCONTENT;
		$newServers = Server::db_getListNew();
		if (!empty($newServers)) {
			$teamLinkBase = 'index.php?employee=TeamAccounts&amp;action=view&amp;id=';
			foreach ($newServers as $server) {
				$teamLink = $teamLinkBase . $server['rühmakonto_id'];
				$table .= <<<ENDCONTENT
	<tr>
		<td>{$server['server']}</td>
		<td>{$server['mängu_nimi']}</td>
		<td><a href="{$teamLink}">{$server['rühma_nimi']}</a></td>
		<td>
			<form method="POST">
				<input type="hidden" name="serverid" value="{$server['mänguserver_id']}" />
				<input class="button" type="submit" name="confirmserver" value="Kinnita" />
				<input class="button" type="submit" name="denyserver" value="Kustuta" />
			</form>
		</td>
	</tr>		
ENDCONTENT;
			}
		} else {
			$table .= '<tr><td colspan="4">Uusi mänguservereid ei ole</td></tr>';
		}
		$table .= "</table>\n";
		return $table;
	}
}
