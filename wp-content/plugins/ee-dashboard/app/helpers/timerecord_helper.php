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
	function createTimerecordTable($maxRecordsToShow = ''){
		global $wpdb;
		$maxRecords = '';
		if($maxRecordsToShow){$maxRecords = "LIMIT ".$maxRecordsToShow;}
		setlocale(LC_ALL, 'nl_NL');
		
		$currentSolveUser = $this->toolbox->getCurrentSolveUser();
		$restult_string = "";
		$details ="";
		$totalBillableHours = 0;
		$totalWorkingHours =0;
		$totalPresentHours = 0;
		$previousDate = "";
		$i = 200;
		$timerecordCounter = 0;
		
		$aTimerecords = $wpdb->get_results("
			SELECT * 
			FROM wp_solve360_timerecord 
			WHERE person = ".$currentSolveUser->id."
			ORDER BY date DESC 
			".$maxRecords." "
		);
		
		$amountOfTimerecords = sizeof($aTimerecords);
		
		foreach($aTimerecords as $timerecord){
			$timerecordCounter++;
			$timerecordDateInYearMonthDay = substr($timerecord->date, 0, 10);
			$previousDateInYearMonthDay = substr($previousDate, 0, 10);
			$equalDates = $this->equalStrings($timerecordDateInYearMonthDay, $previousDateInYearMonthDay );
			$tableBlockOfOneDay = '';
			
			$details = $timerecord->details;
			if($details){
				$details=$timerecord->details;
			}else{
				$details='-';
			}
			$starttijd = explode(":", $timerecord->start);
			$eindtijd = explode(":", $timerecord->eind);
			$iKey = $i.$timerecord->id;
			
			$tableStart = '';
			$tableEnd = '';
			$tableRecord = '';
			
			$tableRecord .= "
				<tr>
					<td style='max-width:400px;'>" . $details. "</td>
					<td style='width:25px;margin:5px 0px 5px 3px;'>" . $this->toolbox->getIcon('tijd_wit') . "</td>
					<td style='min-width:50px;'>". $timerecord->hours . ' uur' . "</td>
					<td style='width:15px;padding:5px 5px 5px 5px;'>" . $this->toolbox->getIcon('taak') . "</td>
					<td style='max-width:300px;'>" . $timerecord->parentcn . "</td>
					<td style='width:19px;padding:5px 5px 5px 10px;'>" . $this->toolbox->getIcon('map') . "</td>
					<td style='width:150px;'>" . $timerecord->itemname . "</td>
					<td style='width:20px;padding:5px 5px 5px 10px;'>" . $this->toolbox->getIcon('stopwatch_play') . "</td>
					<td style='min-width:75px;'>" . $starttijd[0].':'.$starttijd[1] . '-' . $eindtijd[0].':'.$eindtijd[1]. "</td>
		
					<td style='width:17px;padding-left:5px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"deleteTimerecord($timerecord->id,".$iKey++.");\" name=\"delete_button\"><img class='table_timerecord_delete' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/delete.png'></a></td>
	
					<td style='width:17px;padding-left:5px;'><a href=\"#\" id=".$iKey." onclick=\"editTimerecord($timerecord->id, '".$iKey++."','".str_replace('\'','&rsquo;',$timerecord->details)."','".$timerecord->solve_id."',$timerecord->itemid, $timerecord->parentparentid,
						$timerecord->parentid, '".str_replace('\'','&rsquo;',$timerecord->parentcn)."','".(string)$timerecord->date."', $timerecord->billable, $timerecord->hours, '".(string)$timerecord->start."', '".(string)$timerecord->eind."', '".str_replace('\'','&rsquo;',$timerecord->itemname)."')\">
						<img src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/edit.png'/></a>
					</td>
	
	
					<td style='width:17px;padding-left:0px;'><a id=\"".$iKey."\" href=\"#\" onclick=\"loadTask($timerecord->parentid,'',$timerecord->parentparentid,'".$timerecord->itemid."','".str_replace('\'','&rsquo;',$timerecord->parentcn)."','".$timerecord->itemname."','','','".$iKey."')\" name=\"edit_button\">
						<img id='timerecord_table_play' class='table_task_start' name='".$i."' src='".mvc_plugin_app_url(MVCMODULENAME)."public/image/play.png'></a>
					</td>
				</tr>"
			;
			if($timerecordCounter==$amountOfTimerecords){	
				$date = strftime("%A %e %B",strtotime($previousDate));
				if(!$equalDates){
					//create a table block with header from previous timerecord block
					$tableEnd = "</tbody></table><br />";
					$tableStart = $this->createTableHeader($date, $totalWorkingHours);
					$tableBlockOfOneDay .= $tableEnd . $tableStart . $tableRecordsOfOneDay;
					$totalWorkingHours = 0;
					$tableBlockOfAllDays .= $tableBlockOfOneDay;
					$tableRecordsOfOneDay = '';
					$tableBlockOfOneDay= '';
					
					//create header above last timerecord 
					$previousDate = $timerecord->date;
					$date = strftime("%A %e %B",strtotime($previousDate));
					$tableRecordsOfOneDay = $tableRecord;
					$tableEnd = "</tbody></table><br />";
					$totalWorkingHours = $timerecord->hours;
					$tableStart = $this->createTableHeader($date, $totalWorkingHours);
					$tableBlockOfOneDay .= $tableEnd . $tableStart . $tableRecordsOfOneDay;
					$totalWorkingHours = 0;
					$tableBlockOfAllDays .= $tableBlockOfOneDay;
				}
				else{
					$tableEnd = "</tbody></table><br />";
					$totalWorkingHours = $totalWorkingHours + $timerecord->hours;
					$tableStart = $this->createTableHeader($date, $totalWorkingHours);
					$totalWorkingHours = 0;
					$tableRecordsOfOneDay .= $tableRecord;
					$tableBlockOfOneDay .= $tableEnd . $tableStart . $tableRecordsOfOneDay;
					$tableBlockOfAllDays .= $tableBlockOfOneDay;
					$tableRecordsOfOneDay = '';
				}
			}
			
			//wanneer er een datum boven een groep timerecords moet komen
			if(!$equalDates && $previousDate!='' && $timerecordCounter!=$amountOfTimerecords){
				$date = strftime("%A %e %B",strtotime($previousDate));
				
				$tableEnd = "</tbody></table><br />";
				$tableStart = $this->createTableHeader($date, $totalWorkingHours);
				
				$totalWorkingHours = 0;
				$tableBlockOfOneDay .= $tableEnd . $tableStart . $tableRecordsOfOneDay;
				$tableBlockOfAllDays .= $tableBlockOfOneDay;
				$tableRecordsOfOneDay = '';
			};
		
			$i++; 
			$totalWorkingHours = $totalWorkingHours + $timerecord->hours;
			$tableRecordsOfOneDay .= $tableRecord;
			$previousDate = $timerecord->date;
		}
		$timerecordTable = "
			<table class='table table-hover'>
			<thead></thead>
			<tbody>" . $tableBlockOfAllDays . "</tbody></table>"
		;
		return $timerecordTable;
	}
	
	function equalStrings($string1, $string2){
		$equalStrings = '';
		$result = false;
		$equalStrings = strcmp($string1, $string2);
		
		if($equalStrings == 0){
			$result = true;
		}
		return $result;
	}
	function createTableHeader($date, $totalWorkingHours){
		$tableHeader = "<table class='table table-hover'>
					<thead><strong><span style='font-size:18px;'>".$date. "</span><span style='margin-left:10%;'>Werktijd: ".$totalWorkingHours."uur</span></strong></thead>
					<tbody>"
		;
		return $tableHeader;
	}
	
}
?>