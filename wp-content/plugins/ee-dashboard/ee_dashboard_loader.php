<?php

class EeDashboardLoader extends MvcPluginLoader {
	
	var $db_version = '1.0';
	
	function init() {
	
		// Include any code here that needs to be called when this class is instantiated
	
		global $wpdb;
	
		$this->tables = array(
			'solve360_timerecord' => $wpdb->prefix.'solve360_timerecord',
			'solve360_ownership' => $wpdb->prefix.'solve360_ownership',
			'solve360_task' => $wpdb->prefix.'solve360_task',
		);
	}
	
	function activate() {
		// This call needs to be made to activate this app within WP MVC
		
		$this->activate_app(__FILE__);
		
		// Perform any databases modifications related to plugin activation here, if necessary

		require_once ABSPATH.'wp-admin/includes/upgrade.php';
	
		add_option('ee_dashboard_db_version', $this->db_version);
		
		$sql = '
			CREATE TABLE IF NOT EXISTS '.$this->tables['solve360_timerecord'].' (
			id int(10) NOT NULL auto_increment,
			solve_id int(10),
			hash varchar(255),
			type int(3),
			parenttype int(3),
			itemid int(8),
			itemtype int(2),
			parentparentid int(10),
			parentparentcn varchar(255),
			parentid int(10),
			parentcn varchar(255),
			owner int(10),
			created DATETIME,
			date DATETIME,
			person int(10),
			billable int(1),
			details varchar(255),
			hours VARCHAR(5),
			start TIME,
			eind TIME,
			itemname varchar(255),
			PRIMARY KEY  (id)
		)';
  		dbDelta($sql);
  
  		$sql = '
			CREATE TABLE IF NOT EXISTS '.$this->tables['solve360_ownership'].' (
			id int(10) NOT NULL auto_increment,
			name varchar(255),
			email varchar(255),
			preventlogin int(1),
			preventprivatedata int(1),
			administrator int(1),
			PRIMARY KEY  (id)
		)';
  		dbDelta($sql);
  		
  		
  		$sql = '
			CREATE TABLE IF NOT EXISTS wp_solve360_task (
				id int(10) NOT NULL auto_increment,
				solve_id int(10), 
				itemid int(10),
				itemtype int(2),
				typeid int(2),
				parenttypeid int(2),
				position int(10),
				modificatorid int(10),
				title varchar(255),
				duedate DATETIME,
				completedby int(10),
				completedon DATETIME,
				completed INT(1),
				priority INT(5),
				sendnotification INT(1),
				timeremainsmeasure VARCHAR(255),
				assignedto INT(10),
				timeremains INT(5),
				comments VARCHAR(255),
				name VARCHAR(255),
				itemname VARCHAR(255),
				parent INT(10),
				created DATETIME,
				viewed DATETIME,
				updated DATETIME,
				estimated_time varchar(5),
				pricing int(5),
				PRIMARY KEY  (id)
			)';
		dbDelta($sql);
		
		
		$sql = ' 
			CREATE TABLE IF NOT EXISTS wp_solve360_tasklist(
				id INT(10) NOT NULL auto_increment,
				itemid INT(10),
				itemtype INT(5),
				itemname VARCHAR(255),
				parent INT(10),
				typeid INT(5),
				parenttypeid INT(5),
				position INT(10),
				modificatorid INT(10),
				completed INT(1),
				milestonedate DATE,
				attendees VARCHAR(255), 
				name VARCHAR(255),
				description VARCHAR(255),
				created DATETIME,
				viewed DATETIME,
				updated DATETIME,
				PRIMARY KEY (id)
			)';
		dbDelta($sql);
				
	}

	function deactivate() {
	
		// This call needs to be made to deactivate this app within WP MVC
		
		$this->deactivate_app(__FILE__);
		
		// Perform any databases modifications related to plugin deactivation here, if necessary
	
	}

}

?>