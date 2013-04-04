<?php 
class TimerecordingHelper extends MvcHelper {
	
	function __construct(){	
	}
	
	function truncateDatabaseTable(){
		mysql_query('TRUNCATE TABLE wp_ee_current_timerecording');
	}
}
?>