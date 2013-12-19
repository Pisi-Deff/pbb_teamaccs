<?php
class TeamAccountsEmployeePage extends EmployeePage {
	public function setupEmployee() {
		$action = (empty($this->get['action']) ? null : $this->get['action']);
		switch ($action) {
			case 'new':
				$this->actionNew();
				break;
			case 'list':
			case null:
				$this->actionList();
				break;
			case 'view':
				$this->actionView();
				break;
			case 'edit':
				$this->actionEdit();
				break;
			case 'adduser':
				$this->actionAddUser();
				break;
			case 'addserver':
				$this->actionAddServer();
				break;
			case 'addcomment':
				$this->actionAddComment();
				break;
			default:
				self::addMessage(new Message('Tundmatu tegevus!', 'error'));
				break;
		}
	}
	
	private function actionNew() {
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
					$email, $status, $applicationID);
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
	
	private function genTeamAccountForm($name, $website, $email, 
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
	
	private function actionList() {
		$this->setTitle('Rühmakontode nimekiri');
		$this->content .= $this->genTeamAccountsTable();
	}
	
	private function genTeamAccountsTable() {
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
		<td><a class="button" href="{$viewLink}">Vaata</a></td>
	</tr>
ENDCONTENT;
			}	
		} else {
			$table .= '<tr><td colspan="5">Rühmakontosid ei ole</td></tr>';
		}
		$table .= "</table>\n";
		return $table;
	}
	
	private function actionView() {
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
			$addUserLink = $linkBase . 'adduser';
			$addServerLink = $linkBase . 'addserver';
			$addCommentLink = $linkBase . 'addcomment';
			$this->content .= <<<ENDCONTENT
	<table class="content">
		<tr class="toprow"><td colspan="4">Rühma andmed</td></tr>
		<tr><td>Nimi</td><td colspan="3">{$data['rühma_nimi']}</td></tr>
		<tr><td>Veebileht</td><td colspan="3">{$data['rühma_veebileht']}</td></tr>
		<tr><td>Kontaktmeil</td><td colspan="3">{$data['kontaktmeil']}</td></tr>
		<tr><td>Staatus</td><td colspan="3" class="{$statusClass}">{$data['staatus']}</td></tr>
		<tr class="toprow"><td colspan="4">Operatsioonid</td></tr>
		<tr>
			<td>Muuda staatust</td>
			<td colspan="3">
				<form method="POST">
					$statusesSelector
					<input class="button" type="submit" name="changestatus" value="Muuda" />
				</form>
			</td>
		</tr>
		<tr><td colspan="4"><a href="{$editLink}">Muuda andmeid</a></td></tr>
ENDCONTENT;
			$this->genUsersRows($teamAccount, $addUserLink);
			$this->genServersRows($teamAccount, $addServerLink);
			$this->genCommentsRows($teamAccount, $addCommentLink);
			$this->content .= '</table>';
		} else {
			self::addMessage(new Message(
					'Sellise ID-ga rühmakonto puudub', 'error'));
		}
	}
	
	private function genUsersRows($teamAccount, $addUserLink) {
		$this->content .= '<tr class="toprow"><td colspan="4">Kasutajad</td></tr>';
		$users = $teamAccount->db_getUsers();
		if (!empty($users)) {
			foreach ($users as $user) {
				$this->content .= <<<ENDCONTENT
		<tr>
			<td colspan="3">{$user['kasutajanimi']}</td>
			<td>
				<form method="POST">
					<input type="hidden" name="userid" value="{$user['kasutaja_id']}" />
					<input class="button" type="submit" name="deluser" value="Kustuta" />
				</form>
			</td>
		</tr>
ENDCONTENT;
			}
		} else {
			$this->content .= '<tr><td colspan="4">Kasutajad puuduvad</td></tr>';
		}
		$this->content .= '<tr><td colspan="4"><a href="' .
				$addUserLink . '">Lisa kasutaja</a></td></tr>';
	}
	
	private function genServersRows($teamAccount, $addServerLink) {
		$this->content .= 
				'<tr class="toprow"><td colspan="4">Mänguserverid</td></tr>';
		$servers = $teamAccount->db_getServers();
		if (!empty($servers)) {
			foreach ($servers as $server) {
				$IPPort = $server['ip'] . ':' . $server['port'];
				$statusClass = '';
				if ($server['mänguserveri_staatus_id'] === 1) {
					$statusClass = 'deactivated';
				} else if ($server['mänguserveri_staatus_id'] === 2) {
					$statusClass = 'activated';
				}
				$this->content .= <<<ENDCONTENT
		<tr>
			<td>{$IPPort}</td>
			<td>{$server['mängu_nimi']}</td>
			<td class="{$statusClass}">{$server['staatus']}</td>
			<td>
				<form method="POST">
					<input type="hidden" name="serverid" value="{$server['mänguserver_id']}" />
					<input class="button" type="submit" name="delserver" value="Kustuta" />
				</form>
			</td>
		</tr>
ENDCONTENT;
			}
		} else {
			$this->content .= '<tr><td colspan="4">Mänguserverid puuduvad</td></tr>';
		}
		$this->content .= '<tr><td colspan="4"><a href="' .
				$addServerLink . '">Lisa mänguserver</a></td></tr>';
	}
	
	private function genCommentsRows($teamAccount, $addCommentLink) {
		$this->content .= 
				'<tr class="toprow"><td colspan="4">Kommentaarid</td></tr>';
		$comments = $teamAccount->db_getComments();
		if (!empty($comments)) {
			foreach ($comments as $comment) {
				$this->content .= <<<ENDCONTENT
		<tr>
			<td colspan="2">{$comment['kasutajanimi']}</td>
			<td colspan="2">{$comment['lisamise_aeg']}</td>
		</tr>
		<tr>
			<td colspan="4"><textarea disabled>{$comment['tekst']}</textarea></td>
		</tr>
ENDCONTENT;
			}
		} else {
			$this->content .= '<tr><td colspan="4">Kommentaarid puuduvad</td></tr>';
		}
		$this->content .= '<tr><td colspan="4"><a href="' .
				$addCommentLink . '">Lisa kommentaar</a></td></tr>';
	}
	
	private function actionEdit() {
		// TODO
	}
	
	private function actionAddServer() {
		// TODO
	}
	
	private function actionAddUser() {
		// TODO
	}
	
	private function actionAddComment() {
		// TODO
	}
}
