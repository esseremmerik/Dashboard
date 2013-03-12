<?php //echo date("d-m-Y H:i:s");

global $wpdb;

if(!isset($wpdb))
{
    require_once('../../../wp-config.php');
    require_once('../../../wp-load.php');
    require_once('../../../wp-includes/wp-db.php');
}
 if ( is_user_logged_in() ) {
    $table_name = $wpdb->prefix. "solve360_phonerecord";
	$sSQL = "SELECT *, DATE_FORMAT(DATE_ADD(create_datetime, INTERVAL 2 HOUR),'%d-%m-%Y %H:%i:%s') AS datum FROM $table_name ORDER BY create_datetime DESC LIMIT 10" ;
	$phonenumbers = $wpdb->get_results($sSQL, ARRAY_A);
	echo "<table border=\"1\" class=\"table table-striped\">
<thead>
  <tr>
    <th><b>Datum:</b></th>
    <th><b>Nummer:</b></th>
    <th><b>Naam:</b></th>


  </tr>
</thead>
<tbody>";
	foreach ( $phonenumbers as $record ) 
	{
			 $sPN = cleanupPhonenumber($record['phonenumber']);
			 
			 
			 $table_name = $wpdb->prefix. "solve360_contact";


$sSQL = "SELECT user_id, name, company FROM $table_name WHERE mobile LIKE '%$sPN%' OR phone LIKE '%$sPN%'";
$aContact = $wpdb->get_row($sSQL, ARRAY_A);
$aCompanySplit = explode(", ", $aContact['company']);
foreach ($aCompanySplit as $sName) {
		
				 $table_comname = $wpdb->prefix. "solve360_company";


				 $sSQL = "SELECT name, company_id FROM $table_comname WHERE name = '$sName'";
			
				 $aCompany = $wpdb->get_row($sSQL, ARRAY_A);
				 if (isset($aCompany['company_id'])) { $sCompany = "<a href=\"https://secure.solve360.com/company/{$aCompany['company_id']}\" target=\"_blank\">" . $aCompany['name'] . "</a>";} else {  $sCompany = "-";}
}
if (isset($aContact['name'])) { $sCallername = "<a href=\"https://secure.solve360.com/contact/{$aContact['user_id']}\" target=\"_blank\">" . $aContact['name'] . "</a>";} else {  $sCallername = "n/a";}

			 echo "<tr>
				  <td>" . $record['datum'] . "</td>
				  <td><a href=\"?dial=$sPN\">" . $sPN ."</a></td>
				  <td>" . $sCallername . "<br/>$sCompany</td>
				</tr>";
	}
	echo "</tbody></table>";
	}
?>