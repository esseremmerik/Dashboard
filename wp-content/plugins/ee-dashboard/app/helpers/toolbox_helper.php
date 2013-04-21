<?php
class ToolboxHelper extends MvcHelper {

	var $user_info;
	var $user_meta;
	
	var $userEmail;
	var $userToken;
	
	function __construct(){	
		$this->user_info = get_userdata(get_current_user_id());
		$this->user_meta =  get_user_meta(get_current_user_id());
		
		$this->userEmail = $this->user_info->user_email;
		$this->userToken = $this->user_info->solve360_token;
	}
	function getIcon($name){
	    $BASE_URL = mvc_plugin_app_url(MVCMODULENAME) . "public/image/";
	    $ICON_NAME = $name;
	    $ICON_FILE = '.png';
	    $ICON_STRING = "<img src='". $BASE_URL . $ICON_NAME . $ICON_FILE ."'>";
	    return $ICON_STRING;
    }
    function getCurrentSolveUser(){
    	global $wpdb;
    	
    	$solveUserObject = $wpdb->get_row("SELECT * 
							FROM wp_solve360_ownership 
    						WHERE email = '".$this->userEmail."'");
    	return $solveUserObject;
    }
    function dateTimeToDate($dateTimeObject){
    	$dateTimeObject = explode(" ", $dateTimeObject);
    	$dateTimeObject = $dateTimeObject[0];
    	$dateTimeObject = explode("-", $dateTimeObject);
    	$dateTimeObject = $dateTimeObject[2].'-'.$dateTimeObject[1].'-'.$dateTimeObject[0];
    	return $dateTimeObject;
    }
}
?>