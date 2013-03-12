<?php
include('index.html');
function ee_init_home(){
	$aVariables = array();
	$aVariable = array();
	
	if (isset($_GET['update_time'])) {
		$test = 'ffddfd';
		//$solve360Service = new Solve360Service(USER, TOKEN);
		//$oResult = $solve360Service->getTimeTracking($startDate, $endDate);
		//$aVariable['$test'] = $test; 
		$aVariables['$test'] = $test;
		//$aVariables[1]['$test'] . 'lol');
		var_dump($aVariables);
		return ee_init_layout($aVariables);
	}
    return ee_init_layout();
}
?>