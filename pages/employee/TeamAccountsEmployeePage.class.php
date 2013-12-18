<?php
class TeamAccountsEmployeePage extends EmployeePage {
	public function setupEmployee() {
		$action = (empty($this->get['action']) ? null : $this->get['action']);
		switch ($action) {
			case 'new':
				$this->actionNew();
				break;
			case 'view';
				$this->actionView();
				break;
			case 'list':
			case null:
				$this->actionList();
				break;
			default:
				self::addMessage(new Message('Tundmatu tegevus!', 'error'));
				break;
		}
	}
	
	public function actionNew() {
		$this->setTitle('Loo uus rühmakonto');
		$showForm = true;
		if (!empty($this->post)) {
			$name = (empty($this->post['teamname']) ? '' : 
					$this->post['teamname']);
			$website = (empty($this->post['teamwebsite']) ? '' : 
					$this->post['teamwebsite']);
			$email = (empty($this->post['teamemail']) ? '' : 
					$this->post['teamemail']);
			$status = (empty($this->post['teamstatus']) ? '' : 
					$this->post['teamstatus']);
			// todo: applicaton id if sourced from application.
			$teamAccount = TeamAccount::createNew($name, $website, 
					$email, $status);
			if ($teamAccount !== null) {
				redirectLocal('index.php?employee=TeamAccounts&action=view&created=1&id=' . 
						$teamAccount->getID());
				$showForm = false;
			}
		}
		if ($showForm) {
			$this->content .= $this->genTeamAccountForm();
		}
	}
	
	public function actionView() {
		$this->setTitle('Rühmakonto vaade');
		if (!empty($this->get['id']) && is_numeric($this->get['id']) && 
				($id = intval($this->get['id'])) > 0) {
			$teamAccount = new TeamAccount($id);
			if (!empty($this->get['created'])) {
				self::addMessage(new Message('Rühmakonto loodi edukalt!'));
			}
			$this->viewTeamAccount($teamAccount);
		} else {
			self::addMessage(new Message(
					'Rühmakonto ID puudub või ei ole number.', 'error'));
		}
	}
	
	private function viewTeamAccount($teamAccount) {
		$data = $teamAccount->db_getData();
		if (!empty($data)) {
			$statuses = TeamAccount::db_getStatuses();
			$statusesSelector = generateFormSelector($statuses, 
					'teamstatus', 'nimetus');
			$linkBase = 'index.php?employee=TeamAccounts&amp;id=' . 
					$teamAccount->getID() . '&amp;action=';
			$editLink = $linkBase . 'edit';
			$usersLink = $linkBase . 'users';
			$serversLink = $linkBase . 'servers';
			$this->content .= <<<ENDCONTENT
	<table class="content">
		<tr class="toprow"><td colspan="2">Rühma andmed</td></tr>
		<tr><td>Nimi</td><td>{$data['rühma_nimi']}</td></tr>
		<tr><td>Veebileht</td><td>{$data['rühma_veebileht']}</td></tr>
		<tr><td>Kontaktmeil</td><td>{$data['kontaktmeil']}</td></tr>
		<tr><td>Staatus</td><td>{$data['staatus']}</td></tr>
		<tr class="toprow"><td colspan="2">Operatsioonid</td></tr>
		<tr>
			<td>Muuda staatust</td>
			<td>
				<form method="POST">
					$statusesSelector
					<input class="button" type="submit" name="changestatus" value="Muuda" />
				</form>
			</td>
		</tr>
		<tr><td colspan="2"><a href="{$editLink}">Muuda andmeid</a></td></tr>
		<tr><td colspan="2"><a href="{$usersLink}">Kasutajate haldus</a></td></tr>
		<tr><td colspan="2"><a href="{$serversLink}">Mänguserverite haldus</a></td></tr>
	</table>
ENDCONTENT;
		} else {
			self::addMessage(new Message(
					'Sellise ID-ga rühmakonto puudub', 'error'));
		}
	}
	
	public function actionList() {
		$this->setTitle('Rühmakontode nimekiri');
		$this->content .= $this->genTeamAccountsTable();
	}
	
	public function genTeamAccountsTable() {
		$table = <<<ENDCONTENT
<table>
	<tr class="toprow">
		<td>Rühma nimi</td>
		<td>Rühma veebileht</td>
		<td>Kontaktmeil</td>
		<td>Staatus</td>
		<td></td>
	</tr>
ENDCONTENT;
		$teamAccounts = TeamAccount::db_getList();
		$viewLinkBase = 
				'index.php?employee=TeamAccounts&amp;action=view&amp;id=';
		foreach ($teamAccounts as $teamAccount) {
			$viewLink = $viewLinkBase . $teamAccount['rühmakonto_id'];
			$table .= '<tr>';
			$table .= '<td><a href="' . $viewLink . '">' . 
					$teamAccount['rühma_nimi'] . '</a></td>';
			$table .= '<td>' . $teamAccount['rühma_veebileht'] . '</td>';
			$table .= '<td>' . $teamAccount['kontaktmeil'] . '</td>';
			$table .= '<td>' . $teamAccount['staatus'] . '</td>';
			$table .= '<td><a href="' . $viewLink . '">Vaata</a></td>';
			$table .= "</tr>\n";
		}
		$table .= "</table>\n";
		return $table;
	}
	
	public function genTeamAccountForm() {
		$statusSelector = generateFormSelector(TeamAccount::db_getStatuses(), 
				'teamstatus', 'nimetus', 1);
		return <<<ENDCONTENT
	<div class="content">
	<form action="index.php?employee=TeamAccounts&amp;action=new" method="post">
		Rühma nimi:<br />
		<input type="text" name="teamname" /><br />
		Rühma veebileht:<br />
		<input type="text" name="teamwebsite" /><br />
		Rühma kontaktmeil:<br />
		<input type="text" name="teamemail" /><br />
		Rühma staatus:<br />
		{$statusSelector}<br />
		<input class="button" type="submit" name="createta" value="Loo rühmakonto" />
	</form>
	</div>
ENDCONTENT;
	}
}
