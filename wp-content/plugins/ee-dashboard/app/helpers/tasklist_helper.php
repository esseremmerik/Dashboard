<?php 
class TasklistHelper extends MvcHelper {
	
	function __construct(){	
	}
	
	function truncateDatabaseTable(){
		mysql_query('TRUNCATE TABLE wp_solve360_tasklist');
	}
}
?>