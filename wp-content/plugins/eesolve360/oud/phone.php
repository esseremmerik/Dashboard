<?php
// REQUIRED Edit with your email address
define('USER', 'peter@esser-emmerik.nl');
// REQUIRED Edit with token, Workspace > My Account > API Reference > API Token
define('TOKEN', '44V0jaP5R8H4af5e3fg0W2O9j2P4naB2I2z6tdnb');

include 'timetracking_databaseService.php';
include 'timetracking_databaseSettings.php';

// Configure service gateway object
require 'Solve360Service.php';
$solve360Service = new Solve360Service(USER, TOKEN);
$_VOYS = array("url"=>"https://client.voys.nl/api/clicktodial/","hash"=>"6137d4165c15a14606a0bae3f7acc74c3efa6031","urls"=>"http://dev.presteren.nu/phone.php");
$aDialOptions = array("hash"=>$_VOYS['hash'],"a_number"=>"201","b_number"=>"");

/*
hash: jouw hash uit het clientsysteem
a_number: het gewenste telefoonnummer of extensie van de beller (vaak een extensie uit je account)
b_number: het te bellen telefoonnummer of extensie
a_cli (niet verplicht): kan drie waarden bevatten: "a_number", "b_number" of "suppressed". Deze waarde geeft aan welk CLI naar de A-zijde van het gesprek wordt meegestuurd
b_cli (niet verplicht): De mee te sturen CLI naar de B-zijde van het gesprek. Het meegegeven nummer moet vallen binnen de range van de bijbehorende client.
auto_answer (niet verplicht, true/false, standaard false): Bij true wordt bij de A-zijde meteen opgenomen. Dit is nuttig in het geval van een aangesloten koptelefoon.
*/

function cleanNumber($sString) {
		return str_replace(array(" ","(",")","-","+"),array("","","","","00"),$sString);
}
if (isset($_GET['update'])) {
 $oResult = $solve360Service->getContactFields("","");

// var_dump($oResultContact);
//exit();
  // update database (insert all contact records)  
 		$dbresult = $m->execute('TRUNCATE contact');
 //$oResult = $solve360Service->getAllContacts();
    foreach($oResult as $name => $data) {
    	
 //   echo $data->id . ": " . $data->name  . "\n";
  //  $oResultContact = $solve360Service->getContactFields( $data->name);
  //  var_dump($oResultContact);
  $sPhone = $data->businessphonemain;
  if ($sPhone == "") { $sPhone = $data->businessphonedirect; }

  /*if (!($data->name == "")) {
	  echo $data->name . " $sPhone" . $data->cellularphone .  "\n";
  } else { var_dump($data);}*/

   		if (!($data->name == "")) {	
	   		$data_contact = array(
			'user_id' => (string)$data->id,
			'name' => (string)$data->name,
			'phone' => (string)$sPhone,
			'mobile' => (string)$data->cellularphone
			);
			
	
			$dbresult = $m->insert('contact',$data_contact);
		}
    }

}

if (isset($_GET['dial'])) {
	
	if (!($_GET['dial'] == ""))
	{
			
			$aDialOptions['b_number'] = urlencode($_GET['dial']);
			$dataset = http_build_query($aDialOptions);
			$data_string = json_encode($aDialOptions);  
			try {
				do_post_request($_VOYS['url'],$data_string, "Content-type: application/json\r\n Accept: application/json\r\n");
			} 
			catch(Exception $e)
			  {
			  echo 'Message: ' .$e->getMessage();
			  }
	}
	
	//
	
}
function do_post_request_fopen($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}

function do_post_request($url, $data) 
{

//extract data from the post
$fields = $data;
//url-ify the data for the POST
//foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
//rtrim($fields_string, '&');

//open connection
$ch = curl_init();
print_r($data);
//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/json","Content-Length: " . strlen($data))); 
echo $url;
//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

}
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

<table border="1" class="table table-striped">
<thead>
  <tr>
    <th><b>Naam:</b></th>
    <th><b>Telefoon:</b></th>
    <th><b>Mobiel:</b></th>


  </tr>
</thead>
<tbody>
<?php
    
    	$dbresult = $m->select(array("table"=>"contact","limit"=>1000,"order"=>"name"));
    	//var_dump($dbresult);
    foreach($dbresult as $data) {
	    		echo "<tr>
				  <td>" . $data['contact']['name'] . "</td>
				  <td><a href=\"?dial=" . cleanNumber($data['contact']['phone']) . "\">" . $data['contact']['phone'] ."</a></td>
				  <td><a href=\"?dial=" . cleanNumber($data['contact']['mobile']) ."\">" . $data['contact']['mobile'] . "</a></td>
				</tr>";
	   //var_dump($data['contact']);
    }

/*foreach ($oResult->timerecords->timerecord as $timerecord){
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
}*/
?>
<tbody>
</table>
   </body>

</html>