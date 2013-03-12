<?php
/*
Plugin Name: Ee Dashboard
Plugin URI: 
Description: Creation of an ultimate system, risen from the depths of our brains. 
Author: IJsbrand van Prattenburg	
Version: 0.1
Author URI: 
*/

register_activation_hook(__FILE__, 'ee_dashboard_activate');
register_deactivation_hook(__FILE__, 'ee_dashboard_deactivate');

function ee_dashboard_activate() {
	require_once dirname(__FILE__).'/ee_dashboard_loader.php';
	$loader = new EeDashboardLoader();
	$loader->activate();
}

function ee_dashboard_deactivate() {
	require_once dirname(__FILE__).'/ee_dashboard_loader.php';
	$loader = new EeDashboardLoader();
	$loader->deactivate();
}

?>