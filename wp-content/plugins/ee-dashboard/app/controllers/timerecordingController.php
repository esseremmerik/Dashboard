<?php

/**
 * Function to save a timerecording which contains information about a running time of a employee.
 * <br />The idea is that when a employee starts the timer, this function is called.
 * <br />The next information should be in the post:
 * <br />- The value of the text field where you put your activity
 * <br />- The task id which is the task id from Solve360
 * <br />- The task id from WordPress. This is the primary key of the task table.
 * <br />- The tasklist id from WordPress. This is identical to the one from Solve360
 */
function saveTimerecording(){
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

	exit();
}
?>