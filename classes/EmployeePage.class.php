<?php

abstract class EmployeePage extends UIPage {
	public $baseTitleEmployee = 'Töötajapaneel';
	
	public function setup() {
		$this->baseTitle = $this->baseTitleEmployee . ' - ' . $this->baseTitle;
		if (!$this->user->isEmployed()) {
			$this->addMessage(new Message('Ligipääs keelatud!', 'error'));
			return;
		}
		
		$this->setupEmployee();
	}
	
	abstract function setupEmployee();
	
	public function getSidebar() {
		$sidebar = '<nav class="sidebar">';
		$jobs = $this->user->getJobs();
		if (!empty($jobs[1])) {
			$sidebar .= $this->genTeamAccountManagerSidebar($jobs[1]);
		}
		$sidebar .= '</nav>';
		return $sidebar;
	}
	
	public function genTeamAccountManagerSidebar($nimetus) {
		$sidebar = '<span class="sidebarheader">' . $nimetus . '</span><br />';
		$sidebar .= '<a href="index.php?employee=TeamAccounts">Rühmakontode nimekiri</a><br />';
		$sidebar .= '<a href="index.php?employee=AcceptedApplications">Vastuvõetud rühmakontoavaldused</a><br />';
		$sidebar .= '<a href="index.php?employee=TeamAccounts&amp;action=new">Loo uus rühmakonto</a><br />';
		$sidebar .= '<a href="index.php?employee=NewServers">Uued mänguserverid</a><br />';
		return $sidebar;
	}
}