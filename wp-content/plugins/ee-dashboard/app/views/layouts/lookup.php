<?php

require( $_SERVER['DOCUMENT_ROOT'] .'/wp-config.php' );
$mysqli = new mysqli(DB_HOST, DB_USER,DB_PASSWORD, DB_NAME);

if (mysqli_connect_errno())
{
    printf("Er kan geen verbinding worden gemaakt met de database. Foutmelding: %s\n", mysqli_connect_error());
}


if (isset($_GET['query'])) {
	switch ($_GET['action']) {
	
	case 'test': $resultaat = $mysqli->query("SELECT name AS valueresult, user_id AS keyresult FROM wp_solve360_contact WHERE name LIKE '%" . $_GET['query'] . "%' OR company LIKE '%" . $_GET['query'] . "%' ORDER BY name ASC"); break;
	default: $resultaat = $mysqli->query("SELECT t0.*, CONCAT(t0.name, ' [',t0.itemname,']', ' [',t1.name,']') AS valueresult,
										 t0.id as keyresult,
										 t0.completed as completed,
										 t0.duedate as deadline,
										 t0.itemid as itemid,
										 t0.pricing as pricing,
										 t0.parent as tasklistid,
										 t1.name as tasklistname,
										 t0.solve_id as solve_id
										 FROM wp_solve360_task t0 
										 INNER JOIN wp_solve360_tasklist t1
										 ON t0.parent = t1.id
										 WHERE t0.assignedto=" . $_GET['userid'] . " 
										 AND t0.completed='0'
										 AND (t0.title LIKE '%" . $_GET['query'] . "%' OR t0.itemname LIKE '%" . $_GET['query'] . "%') 
										 ORDER BY t0.duedate DESC"); 
}

$aTask = array();
$a = 0;
    while ($record = $resultaat->fetch_assoc())
    {
    	//$tasklist_name=$mysqli->query("SELECT name AS tasklistname FROM wp_solve360_tasklist WHERE id=" . $record['tasklistid'] . "");
    	//$tasklist_name = $tasklist_name->fetch_assoc();
    	$aTask[$a]=array("value"=>$record['valueresult'],
    					"data"=>$record['keyresult'].';;;'.
    							$record['completed'].';;;'.
    							$record['deadline'].';;;'.
    							$record['itemid'].';;;'.
    							$record['pricing'].';;;'.
    							$record['tasklistid'].';;;'.
    							$record['tasklistname'].';;;'.
    							$record['solve_id']
    							);	
    	$a++; 
    }
$aReturn = array("query"=>$_GET['query'],"suggestions"=>$aTask);
echo json_encode($aReturn);

}

$fp = fopen('data.txt', 'w');
fwrite($fp, date('y-m-d H:i:s') . ': ' . serialize($_GET) . " ## SELECT * FROM wp_solve360_task WHERE title LIKE '%" . $_GET['query'] . "%' ORDER BY duedate DESC ## " . json_encode($aReturn) . '\n');
fclose($fp);
?>