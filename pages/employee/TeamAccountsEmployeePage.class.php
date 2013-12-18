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
		$application = null;
		$applicationID = null;
		$name = '';
		$website = '';
		$email = '';
		$status = 1;
		if (!empty($this->get['application']) && 
				is_numeric($this->get['application']) &&
				($id = intval($this->get['application'])) > 0 ) {
			$applicationID = $id;
			$application = new TeamAccountApplication($id);
			$data = $application->getData();
			$name = $data['rühma_nimi'];
			$website = $data['rühma_veebileht'];
			$email = $data['meil'];
		}
		if (!empty($this->post)) {
			$name = (empty($this->post['teamname']) ? $name : 
					$this->post['teamname']);
			$website = (empty($this->post['teamwebsite']) ? $website : 
					$this->post['teamwebsite']);
			$email = (empty($this->post['teamemail']) ? $email : 
					$this->post['teamemail']);
			$status = (empty($this->post['teamstatus']) ? $status : 
					$this->post['teamstatus']);
			$teamAccount = TeamAccount::createNew($name, $website, 
					$email, $status, $application->getID());
			if ($teamAccount !== null) {
				redirectLocal('index.php?employee=TeamAccounts&action=view&created=1&id=' . 
						$teamAccount->getID());
				$showForm = false;
			}
		}
		if ($showForm) {
			$this->content .= $this->genTeamAccountForm($name, $website, 
					$email, $status, $applicationID);
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
			$statusClass = '';
			if ($data['rühmakonto_staatus_id'] === 1) {
				$statusClass = 'activated';
			} else if ($data['rühmakonto_staatus_id'] === 2) {
				$statusClass = 'deactivated';
			}
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
		<tr><td>Staatus</td><td class="{$statusClass}">{$data['staatus']}</td></tr>
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
		if (!empty($teamAccounts)) {
			foreach ($teamAccounts as $teamAccount) {
				$viewLink = $viewLinkBase . $teamAccount['rühmakonto_id'];
				$websiteCell = '';
				if (!empty($teamAccount['rühma_veebileht'])) {
					$websiteCell = '<a href="' . $teamAccount['rühma_veebileht'] . 
							'">' . $teamAccount['rühma_veebileht'] . '</a>';
				}
				$statusClass = '';
				if ($teamAccount['rühmakonto_staatus_id'] === 1) {
					$statusClass = 'activated';
				} else if ($teamAccount['rühmakonto_staatus_id'] === 2) {
					$statusClass = 'deactivated';
				}
				$table .= <<<ENDCONTENT
	<tr>
		<td><a href="{$viewLink}">{$teamAccount['rühma_nimi']}</a></td>
		<td>{$websiteCell}</td>
		<td>{$teamAccount['kontaktmeil']}</td>
		<td class="{$statusClass}">{$teamAccount['staatus']}</td>
		<td><a href="{$viewLink}">Vaata</a></td>
	</tr>
ENDCONTENT;
			}	
		} else {
			$table .= '<tr><td colspan="5">Rühmakontosid ei ole</td></tr>';
		}
		$table .= "</table>\n";
		return $table;
	}
	
	public function genTeamAccountForm($name, $website, $email, 
			$status, $applicationID) {
		$applicationArg = (empty($applicationID) ? '' : 
				'&amp;application=' . $applicationID);
		$statusSelector = generateFormSelector(TeamAccount::db_getStatuses(), 
				'teamstatus', 'nimetus', $status);
		return <<<ENDCONTENT
	<div class="content">
	<form action="index.php?employee=TeamAccounts&amp;action=new{$applicationArg}" method="post">
		Rühma nimi:<br />
		<input type="text" name="teamname" value="{$name}" /><br />
		Rühma veebileht:<br />
		<input type="text" name="teamwebsite" value="{$website}" /><br />
		Rühma kontaktmeil:<br />
		<input type="text" name="teamemail" value="{$email}" /><br />
		Rühma staatus:<br />
		{$statusSelector}<br />
		<input class="button" type="submit" name="createta" value="Loo rühmakonto" />
	</form>
	</div>
ENDCONTENT;
	}
}
