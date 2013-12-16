<?php
class TeamAccountsEmployeePage extends EmployeePage {
	public function setupEmployee() {
		$action = (empty($this->get['action']) ? null : $this->get['action']);
		switch ($action) {
			case 'new':
				$this->setTitle('Loo uus rühmakonto');
				$this->content .= $this->genTeamAccountForm();
				break;
			default:
				$this->setTitle('Rühmakontode nimekiri');
				$this->content .= $this->genTeamAccountsTable();
				break;
		}
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
	<form class="content" action="index.php?employee=NewTeamAccount" method="post">
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
