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
		Rühma nimi<span class="required">*</span>:<br />
		<input type="text" name="teamname" value="{$name}" required /><br />
		Rühma veebileht:<br />
		<input type="text" name="teamwebsite" value="{$website}" /><br />
		Rühma kontaktmeil<span class="required">*</span>:<br />
		<input type="text" name="teamemail" value="{$email}" required /><br />
		Rühma staatus<span class="required">*</span>:<br />
		{$statusSelector}<br />
		<br />
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
	
	private function getTeamAccountFromGet() {
		$teamAccount = null;
		if (!empty($this->get['id']) && is_numeric($this->get['id']) && 
				($id = intval($this->get['id'])) > 0) {
			$teamAccount = new TeamAccount($id);
		} else {
			self::addMessage(new Message(
					'Rühmakonto ID puudub või ei ole number.', 'error'));
		}
		return $teamAccount;
	}
	
	private function actionView() {
		$this->setTitle('Rühmakonto vaade');
		if (($teamAccount = $this->getTeamAccountFromGet()) !== null) {
			if (!empty($this->get['created'])) {
				self::addMessage(new Message('Rühmakonto loodi edukalt!'));
			} else if (!empty($this->get['useradded'])) {
				self::addMessage(new Message('Kasutaja lisatud!'));
			} else if (!empty($this->get['serveradded'])) {
				self::addMessage(new Message('Mänguserver lisatud!'));
			} else if (!empty($this->get['commentadded'])) {
				self::addMessage(new Message('Kommentaar lisatud!'));
			} else if (!empty($this->post['changestatus']) &&
					!empty($this->post['teamstatus'])) {
				$teamAccount->db_changeStatus($this->post['teamstatus']);
			} else if (!empty($this->post['deluser']) &&
					!empty($this->post['userid'])) {
				$teamAccount->removeUser(new User($this->post['userid']));
			} else if (!empty($this->post['delserver']) &&
					!empty($this->post['serverid'])) {
				$teamAccount->deleteServer(new Server($this->post['serverid']));
			}
			$this->addReturnButtons($teamAccount);
			$this->viewTeamAccount($teamAccount);
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
					'teamstatus', 'nimetus', $data['rühmakonto_staatus_id']);
			$linkBase = 'index.php?employee=TeamAccounts&amp;id=' . 
					$teamAccount->getID() . '&amp;action=';
			$editLink = $linkBase . 'edit';
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
			$this->genUsersRows($teamAccount, $linkBase);
			$this->genServersRows($teamAccount, $linkBase);
			$this->genCommentsRows($teamAccount, $linkBase);
			$this->content .= '</table>';
		} else {
			self::addMessage(new Message(
					'Sellise ID-ga rühmakonto puudub', 'error'));
		}
	}
	
	private function genUsersRows($teamAccount, $linkBase) {
		$addUserLink = $linkBase . 'adduser';
		$viewLink = $linkBase . 'view';
		$this->content .= '<tr class="toprow"><td colspan="4">Kasutajad</td></tr>';
		$users = $teamAccount->db_getUsers();
		if (!empty($users)) {
			foreach ($users as $user) {
				$this->content .= <<<ENDCONTENT
		<tr>
			<td colspan="3">{$user['kasutajanimi']}</td>
			<td>
				<form method="POST" action="{$viewLink}">
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
	
	private function genServersRows($teamAccount, $linkBase) {
		$addServerLink = $linkBase . 'addserver';
		$viewLink = $linkBase . 'view';
		$this->content .= 
				'<tr class="toprow"><td colspan="4">Mänguserverid</td></tr>';
		$servers = $teamAccount->db_getServers();
		if (!empty($servers)) {
			foreach ($servers as $server) {
				$IPPort = $server['server'];
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
				<form method="POST" action="{$viewLink}">
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
	
	private function genCommentsRows($teamAccount, $linkBase) {
		$addCommentLink = $linkBase . 'addcomment';
		$viewLink = $linkBase . 'view';
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
		$this->setTitle('Muuda rühmakonto andmeid');
		if (($teamAccount = $this->getTeamAccountFromGet()) !== null) {
			$this->addReturnButtons($teamAccount, true);
			// TODO
		}
	}
	
	private function actionAddServer() {
		$this->setTitle('Lisa mänguserver');
		if (($teamAccount = $this->getTeamAccountFromGet()) !== null) {
			$this->addReturnButtons($teamAccount, true);
			if ($teamAccount->db_getData() !== null) {
				if (!empty($this->post['add'])) {
					$ip = (empty($this->post['ip']) ? '' : $this->post['ip']);
					$port = (empty($this->post['port']) ? '' : $this->post['port']);
					$game = (empty($this->post['game']) ? '' : $this->post['game']);
					if ($teamAccount->createServer($ip, $port, $game)) {
						redirectLocal(
								'index.php?employee=TeamAccounts&action=view&serveradded=1&id=' . 
								$teamAccount->getID());
					}
				}
				$gameSelector = generateFormSelector(Game::db_getGames(), 
						'game', 'mängu_nimi');
				$this->content .= <<<ENDCONTENT
	<form class="content" method="POST">
		IP<span class="required">*</span>:<br />
		<input type="text" name="ip" required /><br />
		Port<span class="required">*</span>:<br />
		<input type="number" name="port" pattern="[0-9]{1,5}" min="1" max="65535" required /><br />
		Mäng<span class="required">*</span>:<br />
		{$gameSelector}<br />
		<br />
		<input class="button" type="submit" name="add" value="Lisa mänguserver" />
	</form>
ENDCONTENT;
			} else {
				self::addMessage(new Message(
						'Sellise ID-ga rühmakonto puudub', 'error'));
			}
		}
	}
	
	private function actionAddUser() {
		$this->setTitle('Lisa kasutaja');
		if (($teamAccount = $this->getTeamAccountFromGet()) !== null) {
			$this->addReturnButtons($teamAccount, true);
			if ($teamAccount->db_getData() !== null) {
				if (!empty($this->post['add'])) {
					$userID = (empty($this->post['userid']) ? '' : $this->post['userid']);
					if ($teamAccount->addUser(new User($userID))) {
						redirectLocal(
								'index.php?employee=TeamAccounts&action=view&useradded=1&id=' . 
								$teamAccount->getID());
					}
				}
				$userSelector = generateFormSelector(User::db_listTeamAccountless(), 
						'userid', 'kasutajanimi');
				$this->content .= <<<ENDCONTENT
	<form class="content" method="POST">
		Kasutaja<span class="required">*</span>:<br />
		{$userSelector}<br />
		<br />
		<input class="button" type="submit" name="add" value="Lisa kasutaja" />
	</form>
ENDCONTENT;
			} else {
				self::addMessage(new Message(
						'Sellise ID-ga rühmakonto puudub', 'error'));
			}
		}
	}
	
	private function actionAddComment() {
		$this->setTitle('Lisa kommentaar');
		if (($teamAccount = $this->getTeamAccountFromGet()) !== null) {
			$this->addReturnButtons($teamAccount, true);
			if ($teamAccount->db_getData() !== null) {
				if (!empty($this->post['add'])) {
					$comment = (empty($this->post['comment']) ? '' : $this->post['comment']);
					if ($teamAccount->db_addComment($this->session->getID(), $comment)) {
						redirectLocal(
								'index.php?employee=TeamAccounts&action=view&commentadded=1&id=' . 
								$teamAccount->getID());
					}
				}
				$this->content .= <<<ENDCONTENT
	<form class="content" method="POST">
		Kommentaar<span class="required">*</span>:<br />
		<textarea name="comment"></textarea><br />
		<br />
		<input class="button" type="submit" name="add" value="Lisa kommentaar" />
	</form>
ENDCONTENT;
			} else {
				self::addMessage(new Message(
						'Sellise ID-ga rühmakonto puudub', 'error'));
			}
		}
	}
	
	private function addReturnButtons($teamAccount = null, $backToTeamAccount = false) {
		if ($teamAccount !== null && $backToTeamAccount) {
			$this->content .= 
					'<a class="button" href="index.php?employee=TeamAccounts&amp;id=' .
					$teamAccount->getID() . '&amp;action=view">Tagasi rühmakonto vaatesse</a>';
		}
		$this->content .= 
				'<a class="button" href="index.php?employee=TeamAccounts">Tagasi rühmakontode nimekirja</a>';
	}
}
