<?php

abstract class EmployeePage extends UIPage {
	public function setup() {
		if ($this->user->isEmployed()) {
			$this->content .= $this->genEmployeeSidebar();
			return true;
		} else {
			$this->addMessage(new Message('Ligipääs keelatud!', 'error'));
			return false;
		}
	}
	
	public function genEmployeeSidebar() {
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
		$sidebar .= '<a href="index.php?employee=teamaccounts">Rühmakontode nimekiri</a><br />';
		$sidebar .= '<a href="index.php?employee=acceptedapplications">Vastuvõetud rühmakonto avaldused</a><br />';
		$sidebar .= '<a href="index.php?employee=newteamaccount">Loo uus rühmakonto</a><br />';
		$sidebar .= '<a href="index.php?employee=newservers">Uued mänguserverid</a><br />';
		return $sidebar;
	}
}
