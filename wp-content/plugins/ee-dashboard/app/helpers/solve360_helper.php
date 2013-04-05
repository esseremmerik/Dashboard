<?php
require_once 'Solve360Service.php';

class Solve360Helper extends MvcHelper {
	var $user_data;
	var $user_meta;
	
	var $userEmail;
	var $userToken;
	
	var $startDate = '2013-02-01';
	var $endDate = '2013-09-01';
	
	
	function __construct(){
		$this->user_info = get_userdata(get_current_user_id());
		$this->user_meta =  get_user_meta(get_current_user_id());
		
		$this->userEmail = $this->user_info->user_email;
		$this->userToken = $this->user_info->solve360_token;
	}
	function update_timerecords(){
		global $wpdb;
		
		$solve360Service = new Solve360Service($this->userEmail, $this->userToken);
		$result = $solve360Service->getTimeTracking($this->startDate, $this->endDate);
		$aReplace = array("T", "+00:00");
		$hash = '';
		foreach ($result->timerecords->timerecord as $timerecord)
		{
			$created = str_replace($aReplace, "", $timerecord->created);
	
			$hash = hash('md5', trim($created.$timerecord->details));
	
			$data = array(
					'solve_id' 		=> $timerecord->id,
					'hash'			=> $hash,
					'type' 			=> $timerecord->type,
					'parenttype' 	=> $timerecord->parenttype,
					'itemid' 		=> $timerecord->itemid,
					'itemtype' 		=> $timerecord->itemtype,
					'parentparentid'=> $timerecord->parentparentid,
					'parentparentcn'=> $timerecord->parentparentcn,
					'parentid' 		=> $timerecord->parentid,
					'parentcn' 		=> $timerecord->parentcn,
					'owner' 		=> $timerecord->owner,
					'created' 		=> $timerecord->created,
					'details' 		=> $timerecord->details,
					'billable' 		=> $timerecord->billable,
					'hours' 		=> $timerecord->hours,
					'person' 		=> $timerecord->person,
					'date' 			=> $timerecord->date,
					'itemname' 		=> $timerecord->itemname,
					'start' 		=> '00:00:00',
					'eind' 			=> '00:00:00',
			);
			$result = $wpdb->insert('wp_solve360_timerecord',$data);
		}
	}
	function update_tasks(){
		global $wpdb;
		$last  			= 'created';
		$type 			= 14;
		
		$solve360Service= new Solve360Service($this->userEmail, $this->userToken);
		$taskArray 		= $solve360Service->getActivities($type, $last, $this->startDate, $this->endDate);
	
		foreach ($taskArray->activities->activity as $task)
		{
			$data = array(
	
					'solve_id' 			=> $task->id,
					'itemid' 			=> $task->itemid,
					'itemtype' 			=> $task->itemtype,
					'typeid' 			=> $task->typeid,
					'parenttypeid'	 	=> $task->parenttypeid,
					'position' 			=> $task->position,
					'modificatorid' 	=> $task->modificatorid,
	
					'title' 			=> $task->fields->title,
					'duedate' 			=> $task->fields->duedate,
					'completedby' 		=> $task->fields->completedby,
					'completedon' 		=> $task->fields->completedon,
					'completed' 		=> $task->fields->completed,
					'priority' 			=> $task->fields->priority,
					'sendnotification'	=> $task->fields->sendnotification,
					'timeremainsmeasure'=> $task->fields->timeremainsmeasure,
					'timeremains' 		=> $task->fields->timeremains,
					'assignedto' 		=> $task->fields->assignedto,
	
					//Custom fields
					'estimated_time' 	=> $task->fields->custom9617245,
					'pricing' 			=> $task->fields->custom10310449,
					//end custom fields
	
					'comments' 			=> $task->comments,
					'name' 				=> $task->name,
					'itemname' 			=> $task->itemname,
					'parent' 			=> $task->parent, //tasklist primary key
					'created' 			=> $task->created,
					'viewed' 			=> $task->viewed,
					'updated' 			=> $task->updated,
			);
			$result = $wpdb->insert($wpdb->prefix. 'solve360_task',$data);
		}
	}
	function update_ownership(){
		global $wpdb;
		$solve360Service= new Solve360Service($this->userEmail, $this->userToken);
		$ownerArray 		= $solve360Service->getOwnership();
	
		foreach ($ownerArray->users->user as $user)
		{
			$data = array(
					'id' 				=> $user->id,
					'name' 				=> $user->name,
					'email' 			=> $user->email,
					'preventlogin' 		=> $user->preventlogin,
					'preventprivatedata'=> $user->preventprivatedata,
					'administrator' 	=> $user->administrator
			);
			$result = $wpdb->insert($wpdb->prefix. 'solve360_ownership',$data);
		}
	}
	function update_tasklist(){
		global $wpdb;
		$solve360Service = new Solve360Service($this->userEmail, $this->userToken);
		$type = 22;
		$last = 'created';
		$result = $solve360Service->getActivities($type, $last, $this->startDate, $this->endDate, $filter = "");
		foreach ($result->activities->activity as $tasklist)
		{
			$data = array(
					'id' => $tasklist->id,
					'itemid' => $tasklist->itemid,
					'itemtype' => $tasklist->itemtype,
					'itemname' => $tasklist->itemname,
					'parent' => $tasklist->parent,
					'typeid' => $tasklist->typeid,
					'parenttypeid' => $tasklist->parenttypeid,
					'position' => $tasklist->position,
					'modificatorid' => $tasklist->modificatorid,
					'name' => $tasklist->name,
					'created' => $tasklist->created,
					'viewed'=> $tasklist->viewed,
					'updated' => $tasklist->updated,
						
					'completed' => $tasklist->fields->completed,
					'milestonedate' => $tasklist->fields->milestonedate,
					'attendees' => $tasklist->fields->attendees,
					'description' => $tasklist->fields->description,
			);
			$result = $wpdb->insert($wpdb->prefix. 'solve360_tasklist',$data);
		}
	}
	
	/*
	 * Sync the WP database and the Solve360 database
	*
	*/
	/*
	function update_solve360_timerecords(){
		global $wpdb;
		$solve360Service = new Solve360Service($user_email, TOKEN);
		$startDate = '2013-02-01';
		$endDate = '2013-03-01';
	
	
		//========= 1. alle timerecords ophalen uit Solve360 ===========//
		$aSolveTimerecords = $solve360Service->getTimeTracking($startDate, $endDate);
	
		//========= 2. Maak van Solve360 tabel een array met hash  ===============//
		$i 						= 0;
		$aSolveTimerecord 		= array();
		$aSolveTimerecordObject = array();
	
		foreach ($aSolveTimerecords->timerecords->timerecord as $object)
		{
			$aSolveTimerecordObject[(int)$object->id] 	= $object;
			$aSolveTimerecord[(int)$object->id]			= hash('md5', trim($object->parentid.$object->parentparentid.$object->details.$object->hours.$object->person));
	
			$i++;
		}
		//var_dump('Solve360 hash:' . $aSolveTimerecord[(int)73730901] . '<br />');
	
		//=========== 3. Maak van de wp tabel een array met hash ======================//
		$i = 0;
		$aWpTimerecords	= $wpdb->get_results("SELECT * FROM wp_solve360_timerecord");
		$aWpTimerecord 	= array();
	
		foreach($aWpTimerecords as $object)
		{
			$aWpTimerecordObject[(int)$object->id]	= $object;
			if(!$object->hash)
			{
				$aWpTimerecord[(int)$object->id]		= hash('md5', trim($object->parentid.$object->parentparentid.$object->details.$object->hours.$object->person));
				$i++;
			}
			else{
				$aWpTimerecord[(int)$object->id] = $object->hash;
			}
		}
		//var_dump('WP hash:' . $aWpTimerecord[(int)96]);
	
		//=========== 4. Vergelijk hash met wp timerecords ===========================//
		$difference = '';
		$difference2= '';
		$difference = array_diff($aSolveTimerecord,$aWpTimerecord); //result zijn de Solve360 records die niet in WP tabel staan
		$difference2= array_diff($aWpTimerecord, $aSolveTimerecord); //result zijn de WP records die niet in Solve360 tabel staan
	
		//======= 5. Stuur de records die uit de vergelijking komen naar Solve360 en WP
		$i 		= 0;
		$data 	= array();
		while($element = current($difference2)){
			echo 'WP key: ' . key($difference2)."<br />";
			$data = array( 
			
			*/
					/*	'id' 			=> $aWpTimerecordObject[(int)key($difference2)]->id, //====
					 'type' 			=> $aWpTimerecordObject[(int)key($difference2)]->type,
			'parenttype' 	=> $aWpTimerecordObject[(int)key($difference2)]->parenttype,
			'itemid' 		=> $aWpTimerecordObject[(int)key($difference2)]->itemid, //===
			'itemtype' 		=> $aWpTimerecordObject[(int)key($difference2)]->itemtype, //===
			'parentparentid'=> $aWpTimerecordObject[(int)key($difference2)]->parentparentid,
			'parentparentcn'=> $aWpTimerecordObject[(int)key($difference2)]->parentparentcn,
			'parent' 		=> $aWpTimerecordObject[(int)key($difference2)]->parentid, //====
			'parentcn' 		=> $aWpTimerecordObject[(int)key($difference2)]->parentcn,
			'owner' 		=> $aWpTimerecordObject[(int)key($difference2)]->owner,
			'created' 		=> $aWpTimerecordObject[(int)key($difference2)]->created, //=====
			'details' 		=> $aWpTimerecordObject[(int)key($difference2)]->details,
			'billable' 		=> $aWpTimerecordObject[(int)key($difference2)]->billable,
			'hours' 		=> $aWpTimerecordObject[(int)key($difference2)]->hours,
			'person' 		=> $aWpTimerecordObject[(int)key($difference2)]->person,
			'date' => '2013-02-12T00:00:00+00:00',
			'start' => '2013-02-12T00:00:00+00:00',
			'end' => '2013-02-12T00:00:00+00:00',
			'timerecorddate' => '2013-02-12T00:00:00+00:00',
			'itemname' 		=> $aWpTimerecordObject[(int)key($difference2)]->itemname,*/
		/*			'parent'	=> $aWpTimerecordObject[(int)key($difference2)]->parentid,
					'data'		=> array(
							'date' => $aWpTimerecordObject[(int)key($difference2)]->date,
							'details' => $aWpTimerecordObject[(int)key($difference2)]->details,
							'hours' => $aWpTimerecordObject[(int)key($difference2)]->hours,
							'person' 		=> $aWpTimerecordObject[(int)key($difference2)]->person,
							'billable' => $aWpTimerecordObject[(int)key($difference2)]->billable,
					),
			);
			//$result = $solve360Service->addItem('projectblogs/timerecord', $data);
			//Wanneer het een bewerkte record is die al bestaat in Solve360, editItem gebruiken (RESTVERB_PUT = update)
			//$result = $solve360Service->editItem('projectblogs/timerecord', $aWpTimerecordObject[(int)key($difference2)]->id, $data);
			//var_dump($result);
			next($difference2);
		}
		if($difference){
			while($element = current($difference)){
				echo 'Solve360 key: ' . key($difference). "<br />";
	
				$data = array(
						'solve_id' 		=> $aSolveTimerecordObject[(int)key($difference)]->id,
						'hash'			=> $aSolveTimerecord[(int)key($difference)],
						'type' 			=> $aSolveTimerecordObject[(int)key($difference)]->type,
						'parenttype' 	=> $aSolveTimerecordObject[(int)key($difference)]->parenttype,
						'itemid' 		=> $aSolveTimerecordObject[(int)key($difference)]->itemid,
						'itemtype' 		=> $aSolveTimerecordObject[(int)key($difference)]->itemtype,
						'parentparentid'=> $aSolveTimerecordObject[(int)key($difference)]->parentparentid,
						'parentparentcn'=> $aSolveTimerecordObject[(int)key($difference)]->parentparentcn,
						'parentid' 		=> $aSolveTimerecordObject[(int)key($difference)]->parentid,
						'parentcn' 		=> $aSolveTimerecordObject[(int)key($difference)]->parentcn,
						'owner' 		=> $aSolveTimerecordObject[(int)key($difference)]->owner,
						'created' 		=> $aSolveTimerecordObject[(int)key($difference)]->created,
						'details' 		=> $aSolveTimerecordObject[(int)key($difference)]->details,
						'billable' 		=> $aSolveTimerecordObject[(int)key($difference)]->billable,
						'hours' 		=> $aSolveTimerecordObject[(int)key($difference)]->hours,
						'person' 		=> $aSolveTimerecordObject[(int)key($difference)]->person,
						'date' 			=> $aSolveTimerecordObject[(int)key($difference)]->date,
						'itemname' 		=> $aSolveTimerecordObject[(int)key($difference)]->itemname,
						'start' 		=> '00:00:00',
						'eind' 			=> '00:00:00',
				);
					
				//$wpdb->insert('wp_solve360_timerecord', $data);
				next($difference);
			}
		}
	*/
		//gebruik editActivity() om alle objecten uit de array naar Solve360 te sturen en te updaten.
	
		/**zie editActivity in Solve360Service
		 * $activityType =
		* $activityId = 'timerecord';
		* $data
		*/
	
}
?>