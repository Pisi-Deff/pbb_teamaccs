<?php
class TeamaccountsEmployeePage extends EmployeePage {
	public function setup() {
		if (parent::setup()) {
			$this->content .= $this->genTeamAccountsTable();
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
}
