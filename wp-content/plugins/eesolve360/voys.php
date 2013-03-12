<?php
//Wordpress deprecated

ini_set('display_errors','on');

//Global WordPress

global $wpdb;

if(!isset($wpdb))
{
    require_once('../../../wp-config.php');
    require_once('../../../wp-load.php');
    require_once('../../../wp-includes/wp-db.php');
}

// log ip address to file
//$oFile = fopen("voys-log.txt","a");
//fwrite($oFile, date("Y-m-d H:i:s: ") . serialize($_REQUEST) . "\n");
//fclose($oFile);

$data_phone = array("phonenumber"=>$_GET['callerid'],"phonename"=>$_GET['callername'],"did"=>$_GET['did'],"code"=>$_GET['code'],"create_datetime"=>current_time('mysql', 1));
$dbresult = $wpdb->insert($wpdb->prefix. 'solve360_phonerecord',$data_phone);
$table_name = $wpdb->prefix. "solve360_contact";
$sPhone = cleanupPhonenumber($data_phone['phonenumber']);

$sSQL = "SELECT name, company FROM $table_name WHERE mobile LIKE '%$sPhone%' OR phone LIKE '%$sPhone%'";
$aContact = $wpdb->get_row($sSQL, ARRAY_A);
if (isset($aContact['name'])) { $sCallername = $aContact['company'] . " " . $aContact['name'];} else {  $sCallername = "n/a";}

echo "status=ACK&callername=$sCallername";
?>