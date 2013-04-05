<?php
class TimetrackingsController extends MvcPublicController {
	
	public function index() {
		//MvcConfiguration::set(array('Debug' => true));
		$this->initialise();
		date_default_timezone_set('Europe/Amsterdam');

		$this->load_model('Timerecord');
		$this->load_model('Task');
		$this->load_model('Timerecording');
	
		
	    $owners = $this->Ownership->find();
	    $this->set('owners', $owners);
	    
	    $owners = $this->Tasklist->find();
	    $this->set('tasklists', $tasklists);
	    
	    $tasks = $this->Task->find();
	    $this->set('tasks', $tasks);
	    
	    $timerecords = $this->Timerecord->find();
	    $this->set('timerecords', $timerecords);
	  
	    $timerecording = $this->Timerecording->find();
	    $this->set('timerecording', $timerecording);
	    
	    $this->render_view('index', array('layout' => 'timetracking'));
    }
    
    function initialise() {
	    define('MVCMODULENAME', 'ee-dashboard');
	  	$this->load_model('Ownership');
	  	$this->load_model('Tasklist');    
	   	$this->current_solve_user();	 
    }
    
    function initialiseTaskTable(){
	    $this->initialise();
		$this->load_model('Timerecord');
		$this->load_model('Task');
		$this->load_model('Tasklist');
		echo $this->showTaskTable();
		exit();
    }
    
    //===============================================================////==================================================================//
    public function showTaskTable(){
    	$current_solve_user = $this->current_solve_user();
    	$restult_string = "";
    	$details ="";
    	
    	$result = $this->Task->find(array(
			'conditions' => array(
				'assignedto' => $current_solve_user->id,
    			'completed' => '0',
			),
			'order' => 'duedate DESC',
			'limit' => 5
		));
		$i=100;
		foreach($result as $object){
			$timerecords = $this->Timerecord->find(array(
										'conditions' => array(
											'person' => $current_solve_user->id,
											'parentid' => $object->solve_id),
			));
			$tasklists = $this->Tasklist->find(array(
										'conditions' => array(
											'id' => $object->parent)
			));
			$spent_time = 0;
			foreach($timerecords as $timerecord){			
				$spent_time += (double)$timerecord->hours;				
			}
			$tasklist_name = '';
			foreach($tasklists as $tasklist){
				$tasklistname = $tasklist->name;
			}
			$timeremains = '-';
			$duedate = '-';
			$duedate = explode(" ", $object->duedate);
			$duedate = $duedate[0];
			$duedate = explode("-", $duedate);
			$duedate = $duedate[2].'-'.$duedate[1].'-'.$duedate[0];
			if($duedate=='00-00-0000'){$duedate='-';}
			$money_icon = $this->getIcon('money');
			$tracked_time = $this->getIcon('tijd_groen');
			if($object->pricing<=0){$money_icon=$this->getIcon('no_payment');}
			if($object->estimated_time>0){$timeremains=$object->estimated_time;}
			if($spent_time>($timeremains*0.75) && $timeremains>0){$tracked_time=$this->getIcon('tijd_oranje');}
			if($spent_time>$timeremains && $timeremains>0){$tracked_time=$this->getIcon('tijd_rood');} 
			if($timeremains>0){$timeremains=$timeremains .  ' uur';}
			$iKey = $i.$object->id;
			$result_string .= "
						<tr id='table_tasks'>    
							<td style='padding:5px 3px 5px 10px;'>" . $this->getIcon('taak') . "</td>
							<td name='".$i."'>" . $object->title . "</td>
							<td style='padding:5px 0px 5px 10px;'>" . $tracked_time . "</td>
							<td name='".$i."' style='width:50px;'>".  $spent_time . ' uur' . "</td>
							<td style='width:20px;padding:5px 1px 5px 5px;'>" . $this->getIcon('klok_wit') . "</td>
							<td name='".$i."' style='width:40px'>".  $timeremains . "</td> 
							<td style='width:25px;padding:3px 0px 3px 10px;'>" . $this->getIcon('deadline_wit') . "</td>
							<td name='".$i."' style='width:70px;'>" . $duedate . "</td>
							<td style='padding:5px 3px 5px 5px;'>" . $this->getIcon('map') . "</td>
							<td name='".$i."'>" . $object->itemname . "</td>
							<td style='width:60px;padding:5px 3px 2px 10px;'>" . '<strong>Geld: </strong>' .  $money_icon . "</td>
							<td style='width:17px;padding-left:20px;'>
								<a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($object->solve_id,$object->pricing,$object->parent,$object->itemid,'".str_replace('\'','&rsquo;',$object->title)."','$object->itemname', '".str_replace('\'','&rsquo;',$spent_time)."', '".str_replace(' uur','',$timeremains)."','".$iKey."', '".$object->id."');\">
									<img class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a></td>		
						</tr>";
				
			$i++;
		}
		$result_string = "<table class='table table-hover'><thead></thead><tbody>" . $result_string . "</tbody></table>";
		return $result_string; 
    }
    
    
    
    
    //==================================================================================////==================================================================================//
    function initialiseAllTasksTable(){
	    $this->initialise();
		$this->load_model('Timerecord');
		$this->load_model('Task');
		$this->load_model('Tasklist');
	    echo $this->showAllTasksTable();
	    exit();
    }
    //==================================================================================//
    function showAllTasksTable(){
    $current_solve_user = $this->current_solve_user();
    	$restult_string = "";
    	$details ="";

	    $result = $this->Task->find(array(
			'conditions' => array(
				'assignedto' => $current_solve_user->id,
    			'completed' => '0',
			),
			'order' => 'duedate DESC',
		));
		$i=100;
		foreach($result as $object){
			$timerecords = $this->Timerecord->find(array(
										'conditions' => array(
											'person' => $current_solve_user->id,
											'parentid' => $object->solve_id),
			));
			$tasklists = $this->Tasklist->find(array(
										'conditions' => array(
											'id' => $object->parent)
			));
			$spent_time = 0;
			foreach($timerecords as $timerecord){			
				$spent_time += (double)$timerecord->hours;				
			}
			$tasklist_name = '';
			foreach($tasklists as $tasklist){
				$tasklistname = $tasklist->name;
			}
			$timeremains = '-';
			$duedate = '-';
			$duedate = explode(" ", $object->duedate);
			$duedate = $duedate[0];
			$duedate = explode("-", $duedate);
			$duedate = $duedate[2].'-'.$duedate[1].'-'.$duedate[0];
			if($duedate=='00-00-0000'){$duedate='-';}
			$money_icon = $this->getIcon('money');
			$tracked_time = $this->getIcon('tijd_groen');
			if($object->pricing<=0){$money_icon=$this->getIcon('no_payment');}
			if($object->estimated_time>0){$timeremains=$object->estimated_time;}
			if($spent_time>($timeremains*0.75) && $timeremains>0){$tracked_time=$this->getIcon('tijd_oranje');}
			if($spent_time>$timeremains && $timeremains>0){$tracked_time=$this->getIcon('tijd_rood');} 
			if($timeremains>0){$timeremains=$timeremains .  ' uur';}
			$iKey = $i.$object->id;
			$result_string .= "
						<tr id='table_tasks'>    
							<td style='padding:5px 3px 5px 10px;'>" . $this->getIcon('taak') . "</td>
							<td name='".$i."'>" . $object->title . "</td>
							<td style='padding:5px 0px 5px 10px;'>" . $tracked_time . "</td>
							<td name='".$i."' style='width:50px;'>".  $spent_time . ' uur' . "</td>
							<td style='width:20px;padding:5px 1px 5px 5px;'>" . $this->getIcon('klok_wit') . "</td>
							<td name='".$i."' style='width:40px'>".  $timeremains . "</td> 
							<td style='width:25px;padding:3px 0px 3px 10px;'>" . $this->getIcon('deadline_wit') . "</td>
							<td name='".$i."' style='width:70px;'>" . $duedate . "</td>
							<td style='padding:5px 3px 5px 5px;'>" . $this->getIcon('map') . "</td>
							<td name='".$i."'>" . $object->itemname . "</td>
							<td style='width:60px;padding:5px 3px 2px 10px;'>" . '<strong>Geld: </strong>' .  $money_icon . "</td>
							<td style='width:17px;padding-left:20px;'>
								<a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($object->id,$object->pricing,$object->parent,$object->itemid,'".str_replace('\'','&rsquo;',$object->title)."','$object->itemname', '".str_replace('\'','&rsquo;',$spent_time)."', '".str_replace(' uur','',$timeremains)."','".$iKey."', '".$object->id."');\">
									<img class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a></td>		
						</tr>";
				
			$i++;
		}
		$result_string = "<table class='table table-hover'><thead></thead><tbody>" . $result_string . "</tbody></table>";
		return $result_string; 

    }
    //==================================================================================////==================================================================================//
    
    
    
    
    
    
    //==================================================================================////==================================================================================//
    function initialiseTimerecordTable(){
	    $this->initialise();
		$this->load_model('Timerecord');
		echo $this->showTimerecordTable();
		exit();
    }
    //==================================================================================//
    public function showTimerecordTable(){
    	$current_solve_user = $this->current_solve_user();
    	$restult_string = "";
    	$details ="";
		
		
		$result = $this->Timerecord->find(array(
			'conditions' => array(
				'person' => $current_solve_user->id,
			),
			'order' => 'created DESC',
			'limit' => 5
		));
		$i = 200;
		foreach($result as $object){	
			$details = $object->details;
			if($details){
				$details=$object->details;
			}else{
				$details='-';
			}
			$starttijd = explode(":", $object->start);
			$eindtijd = explode(":", $object->eind);
			$iKey = $i.$object->id;
			$result_string .= "
						<tr>    
							<td style='max-width:400px;'>" . $details . "</td>
							<td style='width:25px;margin:5px 0px 5px 3px;'>" . $this->getIcon('tijd_wit') . "</td>
							<td style='min-width:50px;'>". $object->hours . ' uur' . "</td> 
							<td style='width:15px;padding:5px 5px 5px 5px;'>" . $this->getIcon('taak') . "</td>
							<td style='max-width:300px;'>" . $object->parentcn . "</td>
							<td style='width:19px;padding:5px 5px 5px 10px;'>" . $this->getIcon('map') . "</td>
							<td style='width:150px;'>" . $object->itemname . "</td>
							<td style='width:20px;padding:5px 5px 5px 10px;'>" . $this->getIcon('stopwatch_play') . "</td>
							<td style='min-width:75px;'>" . $starttijd[0].':'.$starttijd[1] . '-' . $eindtijd[0].':'.$eindtijd[1]. "</td>
							
							<td style='width:17px;padding-left:5px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"deleteTimerecord($object->id,".$iKey++.");\" name=\"delete_button\"><img class='table_timerecord_delete' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/delete.png'></a></td>
							
							<td style='width:17px;padding-left:5px;'><a href=\"#\" id=".$iKey." onclick=\"editTimerecord($object->id, '".$iKey++."','".str_replace('\'','&rsquo;',$object->details)."','".$object->solve_id."',$object->itemid, $object->parentparentid,
							$object->parentid, '".str_replace('\'','&rsquo;',$object->parentcn)."','".(string)$object->date."', $object->billable, $object->hours, '".(string)$object->start."', '".(string)$object->eind."', '".str_replace('\'','&rsquo;',$object->itemname)."')\">
								<img src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/edit.png'/></a>
							</td>
							
							
							<td style='width:17px;padding-left:0px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($object->parentid,'',$object->parentparentid,'".$object->itemid."','".str_replace('\'','&rsquo;',$object->parentcn)."','".$object->itemname."','','','".$iKey."')\" name=\"edit_button\">
								<img id='timerecord_table_play' class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a>
							</td>
						</tr>";
						
						$i++;
			}
			//id,pricing,tasklistid,projectid,taskname,projectname, spent_time, estimated_time,element_id
		$result_string = "<table class='table table-hover'>
							<thead></thead>
								<tbody>" . $result_string . "</tbody></table>";
								
		return $result_string; 
    }        
        
    //====================================================================================////=================================================================================//
    function initialiseAllTimerecordsTable(){
	    $this->initialise();
		$this->load_model('Timerecord');
	    echo $this->showAllTimerecordTable();
	    exit();
    }
    //===========================================================//
    public function showAllTimerecordTable(){
    	$current_solve_user = $this->current_solve_user();
    	$restult_string = "";
    	$details ="";
		
		
		$result = $this->Timerecord->find(array(
			'conditions' => array(
				'person' => $current_solve_user->id,
			),
			'order' => 'created DESC',
		));
		$i = 200;
		foreach($result as $object){	
			$details = $object->details;
			if($details){
				$details=$object->details;
			}else{
				$details='-';
			}
			$starttijd = explode(":", $object->start);
			$eindtijd = explode(":", $object->eind);
			$iKey = $i.$object->id;
			$result_string .= "
						<tr>    
							<td style='max-width:400px;'>" . $details . "</td>
							<td style='width:25px;margin:5px 0px 5px 3px;'>" . $this->getIcon('tijd_wit') . "</td>
							<td style='min-width:50px;'>". $object->hours . ' uur' . "</td> 
							<td style='width:15px;padding:5px 5px 5px 5px;'>" . $this->getIcon('taak') . "</td>
							<td style='max-width:300px;'>" . $object->parentcn . "</td>
							<td style='width:19px;padding:5px 5px 5px 10px;'>" . $this->getIcon('map') . "</td>
							<td style='width:150px;'>" . $object->itemname . "</td>
							<td style='width:20px;padding:5px 5px 5px 10px;'>" . $this->getIcon('stopwatch_play') . "</td>
							<td style='min-width:75px;'>" . $starttijd[0].':'.$starttijd[1] . '-' . $eindtijd[0].':'.$eindtijd[1]. "</td>
							
							<td style='width:17px;padding-left:5px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"deleteTimerecord($object->id,".$iKey++.");\" name=\"delete_button\"><img class='table_timerecord_delete' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/delete.png'></a></td>
							
							<td style='width:17px;padding-left:5px;'><a href=\"#\" id=".$iKey." onclick=\"editTimerecord($object->id, '".$iKey++."','".str_replace('\'','&rsquo;',$object->details)."','".$object->solve_id."',$object->itemid, $object->parentparentid,
							$object->parentid, $object->date, $object->billable, $object->hours, $object->start, $object->eind, '".str_replace('\'','&rsquo;',$object->itemname)."')\"><img src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/edit.png'/></a></td>
							
							
							<td style='width:17px;padding-left:0px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($object->parentid,'',$object->parentparentid,'".$object->itemid."','".str_replace('\'','&rsquo;',$object->parentcn)."','".$object->itemname."','','','".$iKey."')\" name=\"edit_button\">
								<img id='timerecord_table_play' class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a>
							</td>
						</tr>";
						
						$i++;
			}
			//id,pricing,tasklistid,projectid,taskname,projectname, spent_time, estimated_time,element_id
		$result_string = "<table class='table table-hover'>
							<thead></thead>
								<tbody>" . $result_string . "</tbody></table>";
								
		return $result_string; 
    }

	
	//====================================================================================////=================================================================================//
	function initialiseGhostTimerecordsTable(){
	    $this->initialise();
		$this->load_model('Timerecord');
	    echo $this->showGhostTimerecordTable();
	    exit();
    }    
    //===========================================================//

    function showGhostTimerecordTable(){
	    $current_solve_user = $this->current_solve_user();
    	$restult_string = "";
    	$details ="";
		
		
		$result = $this->Timerecord->find(array(
			'conditions' => array(
				'person' => $current_solve_user->id,
				'parentcn' => '',
			),
			'order' => 'created DESC',
		));
		$i = 200;
		foreach($result as $object){	
			$details = $object->details;
			if($details){
				$details=$object->details;
			}else{
				$details='-';
			}
			$starttijd = explode(":", $object->start);
			$eindtijd = explode(":", $object->eind);
			$result_string .= "
						<tr>    
							<td style='max-width:400px;'>" . $details . "</td>
							<td style='width:25px;margin:5px 0px 5px 3px;'>" . $this->getIcon('tijd_wit') . "</td>
							<td style='min-width:50px;'>". $object->hours . ' uur' . "</td> 
							<td style='width:15px;padding:5px 5px 5px 5px;'>" . $this->getIcon('taak') . "</td>
							<td style='max-width:300px;'>" . $object->parentcn . "</td>
							<td style='width:19px;padding:5px 5px 5px 10px;'>" . $this->getIcon('map') . "</td>
							<td style='width:150px;'>" . $object->itemname . "</td>
							<td style='width:20px;padding:5px 5px 5px 10px;'>" . $this->getIcon('stopwatch_play') . "</td>
							<td style='min-width:75px;'>" . $starttijd[0].':'.$starttijd[1] . '-' . $eindtijd[0].':'.$eindtijd[1]. "</td>
							
							<td style='width:17px;padding-left:5px;'><a id=\"".$i."\" href=\"#\" onclick=\"deleteTimerecord($object->id,".$i.");\" name=\"delete_button\"><img class='table_timerecord_delete' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/delete.png'></a></td>
							<td style='width:47px;padding-left:5px;'>" . $this->getIcon('play') . "</td>
						</tr>";
						
						$i++;
		}
		$result_string = "<table class='table table-hover'>
							<thead></thead>
								<tbody>" . $result_string . "</tbody></table>";
		return $result_string; 
    }

    
    function getModalContent(){
    	$type = $_POST['type'];
       	//types:
    	//save_timerecord
    	//delete_timerecord
    	//time_above_estimated
    	
    	//edit_task //en delete
    	//create_task
    	
    	$modal = '';
    	
    	//============ delete_timerecord======================================//
    	if($type=='delete_timerecord'){
    		$modal = "<table>
    					<thead>
    						<tr>
    							<th><center>Weet je het zeker???</center></th>
    						</tr>
    					</thead><tbody><tr><td><input type='hidden' id='modal_timerecordId'/></td></tr></tbody></table>";
		}
		
		//============ confirm_timerecord======================================//
    	if($type=='save_timerecord'){
    		$modal="<table><thead><tr><th>Beschrijving timerecord:</th></tr></thead><tbody><tr><td><input id='modal_beschrijving' type='text' value='' /></td></tr></tbody></table>
    				<table><thead><tr><th>Taak:</th></tr></thead>
    					<tbody>
    						<tr><td><input style='width:500px;' type='text' id='modal_parentcn' class='autocomplete-ajax' placeholder='Benoem taak of project'><div id='selection-ajax'></div></td></tr>
    					    	<td><input type='hidden' id='modal_solveid'/>
	    							<input type='hidden' id='modal_itemid'/>
	    							<input type='hidden' id='modal_parentparentid'/>
	    							<input type='hidden' id='modal_parentid'/>
	    							<input type='hidden' id='modal_key'/>
    							</td>
    					</tbody>
    				</table>
    				<table>
    				 	<thead><tr><th>Duur</th><th>Starttijd</th><th>Eindtijd</th><th>Datum</th><th>Declarabel</th></tr></thead>
    				    <tbody>	
    				    	<tr>
    				     		<td><input name='modal_duur' id='modal_duur' type='text' value='' /></td>
    				     		<td><input id='modal_starttijd' type='text' maxlength='5' value='' /></td>
    				     		<td><input id='modal_eindtijd' type='text' maxlength='5' value='' /></td>
    				     		<td style='vertical-align:top;'><span class='input-append date' id='datepicker' data-date='" . date("d-m-Y") . "' data-date-format='dd-mm-yyyy'>
    				     			<input id='modal_datum' class='span2' size='16' type='text' value='" . date("d-m-Y") . "' readonly><span class='add-on'><i class='icon-calendar'></i></span></span>
    				     		</td>	
    				     		<td><span id='modal_declarabel'></span></td>
    				     	</tr>
       				     </tbody>
    				 </table>
    				 <table><thead><tr><th>Project:</th></tr></thead><tbody><tr><td><span type='text' id='modal_itemname'/></span></td></tr></tbody></table>";
    	}
    	if($type=='time_above_estimated'){
	    	$modal="<table><thead></thead>
    				 	<tbody>
    				 		<tr><td style='width:100px;' ><strong><span type='text'>Project:</span></strong></td><td><span type='text' id='modal_projectname'/></span></td></tr>
    				 		<tr><td style='width:100px;' ><strong><span type='text'>Taak:</span></strong></td><td><span type='text' id='modal_taskname'/></span></td></tr>
    				 	</tbody>
    				 </table>
    				 <br /> 
    				 <table>
    				 	<thead><tr><th>Huidige begroting:</th><th>Geschreven:</th>
    				    <tbody>	
    				    	<tr>
    				     		<td><input style='width:75px;'id='modal_begroot' type='text' value='' disabled='true'/></td>
    				     		<td><input style='width:95px;' id='modal_geschreven' type='text' value='' disabled='true'/></td>
    				     		<td><input type='hidden' style='width:95px;' id='modal_pricing' type='text' value='' disabled='true'/></td>
    				     	</tr>
       				     </tbody>
    				 </table>
    				 <table><thead>
    				 	<tr><th>Wat te doen, wat te doen....kies je redding:</th></tr></thead><tbody><tr>
    				 		<td><div class='btn-group' data-toggle='buttons-radio'>
    				 				<button id='radio_declarabel' type='button' class='btn btn-primary' onclick=\"rDeclarabel()\">Ik werk alleen voor doekoe</button>
    				 				<button id='radio_gratis' type='button' class='btn btn-primary' onclick=\"rFree()\">Ik houd van vrijwilligerswerk</button>
    				 			</div>
    				 		</td>
    				 	</tr>
    				 </tbody></table>";
    	}
    	if($type=='edit_timerecord'){
	    	$modal="<table><thead></thead>
    				 	<tbody>
    				 		<tr><td><strong><span type='text'>Beschrijving:</span></strong></td></tr>
    				 		<tr><td><input type='text' id='modal_beschrijving'/></input></td>
    				 			<td><input type='hidden' id='modal_solveid'/>
	    							<input type='hidden' id='modal_itemid'/>
	    							<input type='hidden' id='modal_parentparentid'/>
	    							<input type='hidden' id='modal_parentid'/>
	    							<input type='hidden' id='modal_key'/>
	    						</td>
	    					</tr>
    				 		<tr><td><strong><span type='text'>Taak:</span></strong></td></tr>
	    					<tr><td><input style='width:500px;' type='text' id='modal_parentcn' class='autocomplete-ajax' placeholder='Benoem taak of project'><div id='selection-ajax'></div></td></tr>
    				 		
	    				</tbody>
    				 </table>
    				 <table><thead></thead>
    				 	<tbody>
    				 		<tr><td><strong><span type='text'>Datum:</span></strong></td>
    				 			<td><strong><span type='text'>Declarabel:</span></strong></td>
    				 			<td><strong><span type='text'>Tijd(uur):</span></strong></td>
    				 			<td><strong><span type='text'>Start:</span></strong></td>
    				 			<td><strong><span type='text'>Eind:</span></strong></td>
    				 		</tr>
    				 		<tr><td><input style='width:150px;'type='text' id='modal_date'/></input></td>
    				 			<td><input style='width:50px;'type='text' id='modal_billable'/></input></td>
    				 			<td><input style='width:50px;'type='text' id='modal_hours'/></input></td>
    				 			<td><input style='width:80px;'type='text' id='modal_start'/></input></td>
    				 			<td><input style='width:80px;'type='text' id='modal_eind'/></input></td></tr>
    				 	</tbody>
    				 </table>
    				 <table><thead></thead>
    				 	<tbody>
    				 		<tr><td><strong><span type='text'>Project:</span></strong></td></tr>
    				 		<tr><td><span type='text' id='modal_itemname'/></span></td></tr>
    				 	</tbody>
    				 </table>";

    	}    	
    	echo $modal;
    	exit();
    }

    


    function generate_lookup_js($action = "default") {
    		$lookup_action = $action; 
    		include( mvc_plugin_app_path(MVCMODULENAME) . "public/js/lookup.js.php");   
    } 
    function current_solve_user(){
    	$current_user = wp_get_current_user();
    	$current_user_email = $current_user->user_email;
    	$current_solve_user = $this->Ownership->find_by_email($current_user_email);
    	foreach($current_solve_user as $user){
    		$current_solve_user = $user;
    		$this->set('current_solve_user', $current_solve_user);
    	}
    return $current_solve_user;
    }
    function getIcon($name){
	    $BASE_URL = mvc_plugin_app_url(MVCMODULENAME) . "public/image/";
	    $ICON_NAME = $name;
	    $ICON_FILE = '.png';
	    $ICON_STRING = '<img src=' . $BASE_URL . $ICON_NAME . $ICON_FILE . '>';
	    return $ICON_STRING;
    }
     
    function save_timerecord(){      	
    	$solve_id		= $_POST['solve_id'];
    	$parent_id 		= $_POST['parentid'];
    	$parent_cn 		= $_POST['parentcn'];
    	$item_id 		= $_POST['itemid'];
    	$details 		= $_POST['details'];
    	$start_time 	= $_POST['start'];
    	$end_time 		= $_POST['eind'];
    	$itemname 		= $_POST['itemname'];
    	$date 			= $_POST['date'];
    	$billable 		= $_POST['billable'];
    	$parentparentid = $_POST['parentparentid'];
    	$gewijzigd		= $_POST['gewijzigd'];
    	
    	$id = $_POST['timerecord_id'];
    	if(!$id){$id = '';}
       	if(!$billable<0){$billable=0;}
    	if($billable>0){$billable=1;}

    	$newDate = date("Y-m-d", strtotime($date));
    	
   		$tijd = explode(":" ,$_POST['hours']); //hh:mm:ss
		$hour = $tijd[0];
		$minutes = $tijd[1];
		$seconds = $tijd[2];
		if($seconds>0 && $minutes==00){$minutes++;}
		$ratio = 60/$minutes;
		$minInDecimalHour = 1/$ratio;
		$total_hour =  number_format($minInDecimalHour + $hour, 2);
		
		$this->initialise();
		$this->load_model('Timerecord');
		$this->load_model('Task');
		
		
		
		
		$aReplace = array("T", "+00:00");
		$created = str_replace($aReplace, "", date('Y-m-d H:i:s'));
		$hash = hash('md5', trim($parent_id.$parentparentid.$details.$total_hour.$this->current_solve_user()->id));
		
		//create record
		$data = array(
			'id' => $id,
			'solve_id' => $solve_id,
			'hash' => $hash,
			'type' => 15,
			'parenttype' => 14,
			'itemid' => $item_id,
			'itemtype' => 2,
			'parentparentid' => $parentparentid,
			'parentparentcn' => '',
			'parentid' => $parent_id,
			'parentcn' => $parent_cn,
			'owner' => 49460117,
			'created' => date('Y-m-d H:i:s'),
			'details' => $details,
			'billable' => $billable,
			'hours' => $total_hour,
			'person' => $this->current_solve_user()->id,
			'date' => $newDate,
			'itemname' => $itemname,
			'start' => $start_time,
			'eind' => $end_time,
			'gewijzigd' => $gewijzigd,
		);
		
		if($id==''){$this->Timerecord->insert($data);} //create timerecord
		else{//update timerecord
			if ($this->Timerecord->save($data)) {
        		$this->flash('notice', 'Successfully saved!');
        	} else {
	        	$this->flash('error', $this->Timerecord->validation_error_html);
	        }
	    }
		exit();
	}
	
	
	
	
	
	
	function delete_timerecord(){
		$timerecord_id = $_POST['timerecordid'];
		
		$this->initialise();
		$this->load_model('Timerecord');
		
		$this->Timerecord->delete($timerecord_id);
		$this->flash('notice', 'Successfully deleted!');
		exit();
	}
	
	
	//=======================Create a task in database====================================////=======================================================================//
	function create_task(){
		$Id = 				isset($_POST['id'])					? $_POST['id']:'';
		$itemId = 			isset($_POST['itemid']) 			? $_POST['itemid']:'';
		$itemType = 		isset($_POST['itemtype']) 			? $_POST['itemtype']:'';
		$typeId = 			isset($_POST['typeid']) 			? $_POST['typeid']:'';
		$parentTypeId = 	isset($_POST['parenttypeid']) 		? $_POST['parenttypeid']:'';
		$position = 		isset($_POST['position']) 			? $_POST['position']:'';
		$modificatorId =	isset($_POST['modificatorid']) 		? $_POST['modificatorid']:'';
		$title = 			isset($_POST['title']) 				? $_POST['title']:'';
		$duedate = 			isset($_POST['duedate']) 			? $_POST['duedate']:'';
		$completedby = 		isset($_POST['completedby'])		? $_POST['completedby']:'';
		$completedon = 		isset($_POST['completedon'])		? $_POST['completedon']:'';
		$completed = 	   	isset($_POST['completed']) 			? $_POST['completed']:'';
		$priority = 		isset($_POST['priority']) 			? $_POST['priority']:'';
		$sendnotification= 	isset($_POST['sendnotification']) 	? $_POST['sendnotification']:'';
		$timeremainsmeasure=isset($_POST['timeremainsmeasure']) ? $_POST['timeremainsmeasure']:'';
		$timeremains = 		isset($_POST['timeremains']) 		? $_POST['timeremains']:'';
		$assignedto = 		isset($_POST['assignedto']) 		? $_POST['assignedto']:'';
		$estimated_time = 	isset($_POST['estimated_time']) 	? $_POST['estimated_time']:'';
		$pricing = 			isset($_POST['pricing']) 			? $_POST['pricing']:'';
		$comments = 		isset($_POST['comments']) 			? $_POST['comments']:'';
		$name = 			isset($_POST['name']) 				? $_POST['name']:'';
		$itemname = 		isset($_POST['itemname'])			? $_POST['itemname']:'';
		$parent = 			isset($_POST['parent']) 			? $_POST['parent']:'';
		$created = 			isset($_POST['created'])			? $_POST['created']:'';
		$viewed = 			isset($_POST['viewed'])				? $_POST['viewed']:'';
		$updated = 			isset($_POST['updated'])			? $_POST['updated']:'';
		
		$data = array(
			'id' 				=> $Id,
			'itemid' 			=> $itemId,
			'itemtype' 			=> $itemType,
			'typeid' 			=> $typeId,
			'parenttypeid' 		=> $parentTypeId,
			'position' 			=> $position,
			'modificatorid' 	=> $modificatorId,
			'title' 			=> $title,
			'duedate' 			=> $duedate,
			'completedby' 		=> $completedby,
			'completedon' 		=> $completedon,
			'completed' 		=> $completed,
			'priority' 			=> $priority,
			'sendnotification' 	=> $sendnotification,
			'timeremainsmeasure'=> $timeremainsmeasure,
			'timeremains' 		=> $timeremains,
			'assignedto' 		=> $assignedto,
			'estimated_time' 	=> $estimated_time,
			'pricing' 			=> $pricing,
			'comments' 			=> $comments,
			'name' 				=> $name,
			'itemname' 			=> $itemname,
			'parent' 			=> $parent,
			'created' 			=> date('Y-m-d H:i:s'),
			'viewed' 			=> $viewed,
			'updated' 			=> $updated,
		);
		
		$this->initialise();
		$this->load_model('Task');
		
		global $wpdb;
		$wpdb->insert('wp_solve360_task', $data);
		
		exit();
	}
	
	
	
	
    function update_task(){    
	    $id = $_POST['taskid'];
	    $estimated_time = $_POST['estimated_time']; //hh:mm
	    $time = explode(':',$estimated_time);
	    $hours = $time[0];
	    $minutes = $time[1];
	    
	    if($time[0]<10){$hours=str_replace('0', '', $time[0]);}
	    if($time[1]<10){$minutes=str_replace('0','',$time[1]);}
	    
	    $ratio = 60/$minutes;
	    $minutesDecimal = 1/$ratio; 
	  
	    $this->load_model('Task');
	    
	    $object = $this->Task->find_by_id($id, array('selects' => array('estimated_time'),));   
	    
	    //sum the old and new time
	    $extra_time = number_format($minutesDecimal + $hours, 2);
	    if($hours==''){$extra_time = $minutesDecimal;}
	    if($minutesDecimal==''){$extra_time = $hours;}
	   
	    $aSum = array($object->estimated_time, $extra_time);
	    $estimated_time = array_sum($aSum);
	    $this->Task->update($id, array('estimated_time' => $estimated_time,
	    								'updated'		=> date('Y-m-d H:i:s')
	    								));
    }
    function updateTaskTaskList(){
		define('BASE_PATH', dirname(dirname(__FILE__)) . "/");
		include BASE_PATH . 'update_wp_database.php';

        $this->load_model('Tasklist');
		$this->Tasklist->delete_all(array('id' => 0));
		
		$this->load_model('Task');
		$this->Task->delete_all(array('id' => 0));
		
		$this->load_model('Ownership');
		$this->Ownership->delete_all(array('id' => 0));
		
		update_tasklists();
		update_tasks();
		update_ownership();
		return;
	}
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
		$autocomplete 		= $_POST['autocomplete'];
		$solve360_taskid 	= $_POST['solve360_taskid'];
		$wp_taskid			= $_POST['wp_taskid'];
		$tasklistid			= $_POST['tasklistid'];
		$details			= $_POST['details'];
		$pricePerHour		= $_POST['pricePerHour'];
		
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
		global $wpdb;
		
		$this->load_model('Timerecording');
		
		$current_solve_user = $this->current_solve_user();
		$timerecordingObject = $this->Timerecording->find_one(array(
				'conditions' => array(
						'wp_user_id' => $current_solve_user->id,
				),
		));
		if(!$timerecordingObject){
			$this->Timerecording->create($data);
		}
		
		exit();
	}
	
	/**
	 * Function to retrieve the timeobject of the current user from the WordPress database. 
	 *
	 */
	function getTimerecording(){
		$this->load_model('Timerecording');
		$this->load_model('Ownership');
		
		$current_solve_user = $this->current_solve_user();

		$timerecordingObject = $this->Timerecording->find_one(array(
				'conditions' => array(
						'wp_user_id' => $current_solve_user->id,
				),
		));
		
		global $wpdb;
		$durationInSeconds = $wpdb->get_row("SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(timestamp_start)) AS timestamp_duration FROM wp_ee_current_timerecording");
		$durationInSeconds = $durationInSeconds->timestamp_duration; 
		$seconds = $durationInSeconds%60;
		if($seconds<10){$seconds = '0'.$seconds;}
		$minutes = floor($durationInSeconds/60);
		if($minutes<10){$minutes='0'.$minutes;}
		$hours = floor(($durationInSeconds/60)/60);
		if($hours<10){$hours='0'.$hours;}
		$durationInHourMinutesSeconds = $hours.':'.$minutes.':'.$seconds;
		
		$startTimeArray = explode(' ', $timerecordingObject->timestamp_start);
		$startTime = $startTimeArray[1];
		$startTimeArray = explode(':', $startTime);
		$startTime = $startTimeArray[0].':'.$startTimeArray[1];
		
		if($timerecordingObject){
			echo $timerecordingObject->autocomplete.';;;'.
				 $timerecordingObject->solve360_task_id.';;;'.
				 $timerecordingObject->wp_task_id.';;;'.
			     $timerecordingObject->tasklist_id.';;;'.
			     $timerecordingObject->beschrijving.';;;'.
				 $durationInHourMinutesSeconds.';;;'.
				 $startTime.';;;'.
				 $timerecordingObject->price_per_hour;
		}
		
		exit();
	}
	function deleteTimeObject(){
		$this->load_model('Timerecording');
		$this->load_model('Ownership');
		
		$current_solve_user = $this->current_solve_user();
		
		$timerecordingObject = $this->Timerecording->find_one(array(
				'conditions' => array(
						'wp_user_id' => $current_solve_user->id,
				),
		));
		if($timerecordingObject){
			$this->Timerecording->delete($timerecordingObject->id);
		}
		exit();
	}
	function fillTimerecordTableFromSolve360(){
		$this->load_helper('solve360');
		$this->solve360->update_timerecords();
	}
	function fillTaskTableFromSolve360(){
		$this->load_helper('solve360');
		$this->solve360->update_tasks();
		
	}
	function fillOwnershipTableFromSolve360(){
		$this->load_helper('solve360');
		$this->solve360->update_ownership();
	
	}
	function fillTasklistTableFromSolve360(){
		$this->load_helper('solve360');
		$this->solve360->update_tasklist();
	
	}
	function truncateTimerecordTable(){
		$this->load_helper('Timerecord');
		$this->timerecord->truncateDatabaseTable();
	}
	function truncateTaskTable(){
		$this->load_helper('Task');
		$this->task->truncateDatabaseTable();
	}
	function truncateOwnershipTable(){
		$this->load_helper('Ownership');
		$this->ownership->truncateDatabaseTable();
	}
	function truncateTasklistTable(){
		$this->load_helper('Tasklist');
		$this->tasklist->truncateDatabaseTable();
	}
}
?>
