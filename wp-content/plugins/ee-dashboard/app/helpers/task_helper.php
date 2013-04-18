<?php 
include_once 'toolbox_helper.php';

class TaskHelper extends MvcHelper {
	var $toolbox;
	var $user_info;
	var $user_meta;
	var $userEmail;
	var $userToken;
	
	function __construct(){	
		$this->user_info = get_userdata(get_current_user_id());
		$this->user_meta = get_user_meta(get_current_user_id());
		$this->userEmail = $this->user_info->user_email;
		$this->userToken = $this->user_info->solve360_token;
		$this->toolbox   = new ToolboxHelper();
	}
	
	function truncateDatabaseTable(){
		mysql_query('TRUNCATE TABLE wp_solve360_task');
	}
	
	function createTaskTable($maxRecordsToShow = ''){
		global $wpdb;
		setlocale(LC_ALL, 'nl_NL');
		
		$maxRecords 	  = '';
		$currentSolveUser = $this->toolbox->getCurrentSolveUser();
		$restult_string   = "";
		$details 		  = "";
		$i				  = 100;
		
		if($maxRecordsToShow){
			$maxRecords = "LIMIT ".$maxRecordsToShow;
		}
		$aTask = $wpdb->get_results("
			SELECT * 
			FROM wp_solve360_task 
			WHERE assignedto = ".$currentSolveUser->id." AND completed=0
			ORDER BY duedate DESC 
			".$maxRecords." "
		);
		
		foreach($aTask as $task){
			$spent_time 	= 0;
			$tasklist_name 	= '';
			$timeremains 	= '-';
			$duedate 		= $aTask->duedate;
			$money_icon 	= $this->toolbox->getIcon('money');
			$tracked_time 	= $this->toolbox->getIcon('tijd_groen');
			$iKey 			= $i . $task->id;
			
			$aTimerecords = $wpdb->get_results("
				SELECT * 
				FROM wp_solve360_timerecord 
				WHERE person = ".$currentSolveUser->id." && parentid = ". $task->solve_id." "
			);
			$aTasklists = $wpdb->get_results("
				SELECT *
				FROM wp_solve360_tasklist
				WHERE id = ".$task->parent." "
			);
			foreach($aTimerecords as $timerecord){
				$spent_time += (double)$timerecord->hours;
			}
			foreach($aTasklists as $tasklist){
				$tasklistname = $tasklist->name;
			}
			
			//convert duedate which is a datetime object to a date object
			$this->toolbox->dateTimeToDate($duedate);
			
			if($duedate=='00-00-0000'){
				$duedate='-';
			}
			if($task->pricing <= 0){
				$money_icon = $this->toolbox->getIcon('no_payment');
			}
			if($task->estimated_time > 0){
				$timeremains = $task->estimated_time;
			}
			if($spent_time>($timeremains*0.75) && $timeremains>0){
				$tracked_time = $this->toolbox->getIcon('tijd_oranje');
			}
			if($spent_time>$timeremains && $timeremains>0){
				$tracked_time = $this->toolbox->getIcon('tijd_rood');
			}
			if($timeremains > 0){
				$timeremains .= ' uur';
			}
			$result_string .= "
						<tr id='table_tasks'>
							<td style='padding:5px 3px 5px 10px;'>" . $this->toolbox->getIcon('taak') . "</td>
							<td name='".$i."'>" . $task->title . "</td>
							<td style='padding:5px 0px 5px 10px;'>" . $tracked_time . "</td>
							<td name='".$i."' style='width:50px;'>".  $spent_time . ' uur' . "</td>
							<td style='width:20px;padding:5px 1px 5px 5px;'>" . $this->toolbox->getIcon('klok_wit') . "</td>
							<td name='".$i."' style='width:40px'>".  $timeremains . "</td>
							<td style='width:25px;padding:3px 0px 3px 10px;'>" . $this->toolbox->getIcon('deadline_wit') . "</td>
							<td name='".$i."' style='width:70px;'>" . $duedate . "</td>
							<td style='padding:5px 3px 5px 5px;'>" . $this->toolbox->getIcon('map') . "</td>
							<td name='".$i."'>" . $task->itemname . "</td>
							<td style='width:60px;padding:5px 3px 2px 10px;'>" . '<strong>Geld: </strong>' .  $money_icon . "</td>
							<td style='width:17px;padding-left:20px;'>
								<a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($task->solve_id,$task->pricing,$task->parent,$task->itemid,'".str_replace('\'','&rsquo;',$task->title)."','$task->itemname', '".str_replace('\'','&rsquo;',$spent_time)."', '".str_replace(' uur','',$timeremains)."','".$iKey."', '".$task->id."');\">
									<img class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a></td>
						</tr>";
		
			$i++;
		}
		$result_string = "<table class='table table-hover'><thead></thead><tbody>" . $result_string . "</tbody></table>";
		return $result_string;
	}
}
?>