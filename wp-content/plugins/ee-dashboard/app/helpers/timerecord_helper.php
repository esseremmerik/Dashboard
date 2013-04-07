<?php
include 'toolbox_helper.php';
 
class TimerecordHelper extends MvcHelper {
	var $toolbox;
	
	var $user_info;
	var $user_meta;
	
	var $userEmail;
	var $userToken;
	
	function __construct(){	
		$this->user_info = get_userdata(get_current_user_id());
		$this->user_meta =  get_user_meta(get_current_user_id());
		
		$this->userEmail = $this->user_info->user_email;
		$this->userToken = $this->user_info->solve360_token;
		
		$this->toolbox = new ToolboxHelper();
	}
	
	function truncateDatabaseTable(){
		mysql_query('TRUNCATE TABLE wp_solve360_timerecord');
		echo 'succes';
	}
	function createTimerecordTable(){
		global $wpdb;
		
		$currentSolveUser = $this->toolbox->getCurrentSolveUser();
		$restult_string = "";
		$details ="";
		
		$result = $wpdb->get_results("SELECT * 
							FROM wp_solve360_timerecord 
							WHERE person = ".$currentSolveUser->id."
							ORDER BY created DESC 
							LIMIT 5");
		
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
							<td style='width:25px;margin:5px 0px 5px 3px;'>" . $this->toolbox->getIcon('tijd_wit') . "</td>
							<td style='min-width:50px;'>". $object->hours . ' uur' . "</td>
							<td style='width:15px;padding:5px 5px 5px 5px;'>" . $this->toolbox->getIcon('taak') . "</td>
							<td style='max-width:300px;'>" . $object->parentcn . "</td>
							<td style='width:19px;padding:5px 5px 5px 10px;'>" . $this->toolbox->getIcon('map') . "</td>
							<td style='width:150px;'>" . $object->itemname . "</td>
							<td style='width:20px;padding:5px 5px 5px 10px;'>" . $this->toolbox->getIcon('stopwatch_play') . "</td>
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
	
}
?>