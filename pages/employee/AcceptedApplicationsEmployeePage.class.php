<?php
class AcceptedApplicationsEmployeePage extends EmployeePage {
	public function setupEmployee() {
		$this->setTitle('Vastuvõetud rühmakontoavaldused');
		$this->content .= $this->genAcceptedApplicationsTable();
	}
	
	public function genAcceptedApplicationsTable() {
		$table = <<<ENDCONTENT
<table>
	<tr class="toprow">
		<td>Rühma nimi</td>
		<td>Rühma veebileht</td>
		<td>Esindaja</td>
		<td>Kontaktmeil</td>
		<td></td>
	</tr>
ENDCONTENT;
		$applications = TeamAccountApplication::db_getList();
		if (!empty($applications)) {
			foreach ($applications as $application) {
				$createTALink = 
						'index.php?employee=TeamAccounts&amp;action=new&amp;application='. 
						$application['rühmakonto_avaldus_id'];
				$websiteCell = '';
				if (!empty($application['rühma_veebileht'])) {
					$websiteCell = '<a href="' . $application['rühma_veebileht'] . 
							'">' . $application['rühma_veebileht'] . '</a>';
				}
				$table .= <<<ENDCONTENT
	<tr>
		<td>{$application['rühma_nimi']}</td>
		<td>{$websiteCell}</td>
		<td>{$application['kasutajanimi']}</td>
		<td>{$application['meil']}</td>
		<td><a href="{$createTALink}">Loo rühmakonto</a></td>
	</tr>
ENDCONTENT;
			}
		} else {
			$table .= '<tr><td colspan="5">Uusi vastuvõetud rühmakontoavaldusi ei ole</td></tr>';
		}
		$table .= "</table>\n";
		return $table;
	}
}
