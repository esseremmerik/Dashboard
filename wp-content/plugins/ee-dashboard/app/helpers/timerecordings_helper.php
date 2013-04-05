<?php

class TimerecordingsHelper extends MvcHelper {
  
	function saveTijdje(){
		$this->load_model('Ownership');
		$current_solve_user = $this->current_solve_user();
		$solve360_user_id = $current_solve_user->id;
	
		$data = array(
				'wp_user_id'	  => $solve360_user_id,
				'autocomplete'	  => $_POST['autocomplete'],
				'solve360_task_id'=> $_POST['solve360_taskid'],
				'wp_task_id'	  => $_POST['wp_taskid'],
				'tasklist_id'	  => $_POST['tasklistid'],
				'beschrijving'	  => $_POST['details'],
				'price_per_hour'  => $_POST['pricePerHour'],
		);
	
		$this->load_model('Timerecording');
	
		$timerecordingObject = $this->Timerecording->find_one_by_wp_user_id($current_solve_user->id);
	
		if(!$timerecordingObject){
			$this->Timerecording->create($data);
		}
		echo 'timerecordingController';
		
		return;
	}
  
}

?>