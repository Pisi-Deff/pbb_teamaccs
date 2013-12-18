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
		foreach ($applications as $application) {
			$createTALink = 
					'index.php?employee=TeamAccounts&amp;action=new&amp;application='. 
					$application['rühmakonto_avaldus_id'];
			$websiteCell = '';
			if (!empty($application['rühma_veebileht'])) {
				$websiteCell = '<a href="' . $application['rühma_veebileht'] . 
						'">' . $application['rühma_veebileht'] . '</a>';
			}
			$table .= '<tr>';
			$table .= '<td>' . $application['rühma_nimi'] . '</td>';
			$table .= '<td>' . $websiteCell . '</td>';
			$table .= '<td>' . $application['kasutajanimi'] . '</td>';
			$table .= '<td>' . $application['meil'] . '</td>';
			$table .= '<td><a href="' . $createTALink . '">Loo rühmakonto</a></td>';
			$table .= "</tr>\n";
		}
		$table .= "</table>\n";
		return $table;
	}
}
