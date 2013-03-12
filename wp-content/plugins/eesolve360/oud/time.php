<?php
// REQUIRED Edit with your email address
define('USER', 'peter@esser-emmerik.nl');
// REQUIRED Edit with token, Workspace > My Account > API Reference > API Token
define('TOKEN', '44V0jaP5R8H4af5e3fg0W2O9j2P4naB2I2z6tdnb');

// Configure service gateway object
require 'Solve360Service.php';
$solve360Service = new Solve360Service(USER, TOKEN);

//datum ophalen uit het html invoerveld
$date = '';
if(isset ($_POST)){ //wanneer er een POST gedaan is
	$date = $_POST['datum'];
}
else{
	$date = date("d-m-Y");
}
$dateInputStart = date("Y-m-d", strtotime($date));

//Datum die geselecteerd is + 7 dagen
$dateInputEndTimestamp = strtotime(date("Y-m-d", strtotime($dateInputStart)) . " +1 week");
$dateInputEnd = date("Y-m-d", $dateInputEndTimestamp);

$oResult = $solve360Service->getTimeTracking($dateInputStart, $dateInputEnd);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Timetracking!</title>
      <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="time_calendar/datepicker.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script src="time_calendar/bootstrap-datepicker.js"></script>
</head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Timetracking</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">Config</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <h1>Dashboard</h1>
      <form method = "post" action="">
      	<div class="input-append date" id="datepicker" data-date="<?php echo date("d-m-Y")?>" data-date-format="dd-mm-yyyy">
	      <input class="span2" size="16" type="text" name="datum" value="">
	      <?php //echo date("d-m-Y")?>
	      <span class="add-on"><i class="icon-th"></i></span>
	    </div>
	    <input type="Submit" value="Zoeken">
      </form>
      <script type="text/javascript">$(document).ready(function() {
      	$('#datepicker').datepicker();
      }); </script>

<table border="1" class="table table-striped">
<thead>
  <tr>
    <th><b>Datum:</b></th>
    <th><b>Taak:</b></th>
    <th><b>Geschatte tijd:</b></th>
    <th><b>Werkelijke tijd:</b></th>
    <th width="350px"><b>Opmerking:</b></th>
  </tr>
</thead>
<tbody>
<?php
foreach ($oResult->timerecords->timerecord as $timerecord){
	if($timerecord->person == '57120668'){ // 57120668 = IJsbrand
		$dateTaskAdded = str_replace("T00:00:00+00:00", "", $timerecord->date);
		$dateTaskAddedFinal = date("d-m-Y", strtotime($dateTaskAdded));

        //Records sorteren door datum om te zetten naar een int
        $date_to_number = str_replace("-", "", $dateTaskAdded);
        $aDatum[$date_to_number] = $date_to_number; //datum als int in array zetten

		$pieces = explode(":", $timerecord->parentcn);
		$estimatedTime = $pieces[0];
		$task = $pieces[1];
		echo "<tr>
				  <td>" . $dateTaskAddedFinal . "</td>
				  <td>" . $task . "<br /></td>
				  <td>" . $estimatedTime . " uur<br /></td>
				  <td>" . $timerecord->hours . " uur<br /></td>
				  <td>" . $timerecord->details . "<br /></td>
				</tr>";
   }
}
?>
<tbody>
</table>
   </body>

</html>