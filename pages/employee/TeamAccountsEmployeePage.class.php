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
			default:
				self::addMessage(new Message('Tundmatu tegevus!', 'error'));
				break;
		}
	}
	
	public function actionNew() {
		$this->setTitle('Loo uus rühmakonto');
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
				redirectLocal('index.php?employee=TeamAccounts&action=view&id=' . 
						$teamAccount->getID());
			}
		}
			$this->content .= $this->genTeamAccountForm();
		}
	
	public function actionList() {
		$this->setTitle('Rühmakontode nimekiri');
		$this->content .= $this->genTeamAccountsTable();
	}
	
	public function genTeamAccountsTable() {
		return <<<ENDCONTENT
<table>
	<tr>
		<td>Rühma nimi</td>
		<td>Rühma veebileht</td>
		<td>Kontaktmeil</td>
		<td>Staatus</td>
		<td></td>
	</tr>
</table>
ENDCONTENT;
	}
	
	public function genTeamAccountForm() {
		$statusSelector = generateFormSelector(TeamAccount::db_getStatuses(), 
				'teamstatus', 'nimetus', 1);
		return <<<ENDCONTENT
	<div class="content">
	<form class="content" action="index.php?employee=TeamAccounts&amp;action=new" method="post">
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
