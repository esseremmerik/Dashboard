<?php
/*
Plugin Name: Solve360 Interface by esser-emmerik
Plugin Script: eesolve360.php
Plugin URI: http://.../eesolve360 (where should people go for this plugin?)
Description: Solve 360 interface
Version: 0.1
Author: Peter Esser
Author URI: http://presteren.nu
Template by: http://web.forret.com/tools/wp-plugin.asp

=== RELEASE NOTES ===
2012-09-25 - v1.0 - first version
*/
include ('Home/index.php');
//include ('Home/index.phtml');


function eesolve360_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.3", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'eesolve360_requires_wordpress_version' );
add_action('admin_init', 'eesolve360_init' );
add_action('admin_menu', 'eesolve360_add_options_page');

 register_activation_hook(__FILE__, 'do_activate');
 register_deactivation_hook(__FILE__, 'do_deactivate');
 register_uninstall_hook(__FILE__, 'do_uninstall');
// uncomment next line if you need functions in external PHP script;
// include_once(dirname(__FILE__).'/some-library-in-same-folder.php');
add_shortcode('solvetimetrack', 'eesolve360_timetrack');
add_shortcode('dashboard', 'eesolve360_show_dashboard');
add_shortcode('phonecontact', 'eesolve360_contactlist');
add_shortcode('phonemonitor', 'eesolve360_phonemonitor');
add_shortcode('extensionlist', 'eesolve360_extensionlist');

function eesolve360_init(){
	register_setting( 'eesolve360_plugin_options', 'eesolve360_options', 'eesolve360_validate_options' );
}

function eesolve360_add_options_page() {
	add_options_page('Solve360 Options Page', 'Solve30 settings', 'manage_options', __FILE__, 'eesolve360_render_form');
}
// ------------------
// eesolve360 parameters will be saved in the database
function eesolve360_add_options() {
// eesolve360_add_options: add options to DB for this plugin
add_option('eesolve360_nb_widgets', '75');
// add_option('eesolve360_...','...');
}
eesolve360_add_options();


function do_activate() 
{
	//echo "geactiveerd!!";
global $wpdb;
$table_name = $wpdb->prefix. "solvetest";
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  name tinytext NOT NULL,
  text text NOT NULL,
  url VARCHAR(55) DEFAULT '' NOT NULL,
  UNIQUE KEY id (id)
);";
$table_name = $wpdb->prefix. "solve360_timerecord";
$sql_time = "
CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `parenttype` int(2) NOT NULL,
  `itemid` int(8) NOT NULL,
  `itemtype` int(1) NOT NULL,
  `parentparentid` int(8) NOT NULL,
  `parentparentcn` varchar(255) NOT NULL,
  `parentid` int(8) NOT NULL,
  `parentcn` varchar(255) NOT NULL,
  `owner` int(10) NOT NULL,
  `created` date NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `billable` int(1) DEFAULT NULL,
  `hours` varchar(10) NOT NULL,
  `person` int(10) NOT NULL,
  `date` date NOT NULL,
  `itemname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0" ;
$table_name = $wpdb->prefix. "solve360_contact";
$sql_contact = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `user_id` int(11) NOT NULL,
  `name` char(50) NOT NULL,
  `mobile` char(20) NOT NULL,
  `phone` char(20) NOT NULL,
  `update` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql_time);
dbDelta($sql_contact);	
}

function do_deactivate() {
	  global $wpdb;
        $table = $wpdb->prefix."solvetest";

        //Delete any options thats stored also?
	//delete_option('wp_yourplugin_version');

	$e = $wpdb->query("DROP TABLE IF EXISTS $table");

}

function do_unistall() {
	

}
function cleanNumber($sString) {
		return str_replace(array(" ","(",")","-","+"),array("","","","","00"),$sString);
}

function eesolve360_timetrack()
{		
	return ee_init_home();
}



function eesolve360_extensionlist(){
	$aExt = array(201,202,203,204,205);
	if (simpleSessionGet("ext") == "") { simpleSessionSet("ext",(get_user_meta($current_user->id,"phone_extension",true)));}
	    //echo simpleSessionGet("ext") . "<ul id=\"ext-list\">";
	
	if (isset($_GET['setext'])) { simpleSessionSet("ext",$_GET['setext']);}
	foreach ($aExt as $sKey)
	{
		$sClass = "";
		if (simpleSessionGet("ext") == $sKey) {
			$sClass= "actief";
		}
		echo "<li class=\"$sClass\"><a href=\"?setext=$sKey\">$sKey</a></li>";
	}
	echo "</ul>";
}
function eesolve360_contactlist($atts, $content = null) {
	
	$current_user = wp_get_current_user();
	$_token = (get_user_meta($current_user->id,"solve360_token",true));
	$_user= $current_user->user_email;
	//echo "$_user $_token";
	include_once(dirname(__FILE__).'/Solve360Service.php');
	$solve360Service = new Solve360Service($_user, $_token);
	//$_VOYS = array("url"=>"https://client.voys.nl/api/clicktodial/","hash"=>"6137d4165c15a14606a0bae3f7acc74c3efa6031","urls"=>"http://dev.presteren.nu/phone.php");
	//$aDialOptions = array("hash"=>$_VOYS['hash'],"a_number"=>"201","b_number"=>"");

	eesolve360_update_contacts($solve360Service);
	eesolve360_show_phonelist();
	if (isset($_GET['dial'])) {
		eesolve360_do_dial();
	}
	
}

function eesolve360_do_dial(){
	$_VOYS = array("url"=>"https://client.voys.nl/api/clicktodial/","hash"=>"6137d4165c15a14606a0bae3f7acc74c3efa6031");
	$current_user = wp_get_current_user();
	//$_ext = (get_user_meta($current_user->id,"phone_extension",true));
	if (simpleSessionGet("ext") == "") { simpleSessionSet("ext",(get_user_meta($current_user->id,"phone_extension",true)));}
	
	$_ext = simpleSessionGet("ext",(get_user_meta($current_user->id,"phone_extension",true)));
	

	/*the simpleSessionSet($key, $value) function sets a session value
the simpleSessionGet($key, $default*/
	$aDialOptions = array("hash"=>$_VOYS['hash'],"a_number"=>$_ext,"b_number"=>"");
	
	
	
		if (!($_GET['dial'] == ""))
		{
				
				$aDialOptions['b_number'] = urlencode($_GET['dial']);
				$dataset = http_build_query($aDialOptions);
				$data_string = json_encode($aDialOptions);  
				try {
					do_post_request($_VOYS['url'],$data_string);
				} 
				catch(Exception $e)
				  {
				  echo 'Message: ' .$e->getMessage();
				  }
		}
		
		//
	
	
}

function do_post_request($url, $data) 
{

		//extract data from the post
		$fields = $data;

		//open connection
		$ch = curl_init();
		//print_r($data);
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/json","Content-Length: " . strlen($data))); 
		
		//execute post
		$result = curl_exec($ch);
		
		//close connection
		curl_close($ch);

}
function eesolve360_phonemonitor()
{
/*
Example jquery interval refresh

$(function() {
    setInterval(function() {
        $.ajax({
            type: "GET",
            data: "v1="+v1+"&v2="+v2,
            url: "location/of/server/script.php",
            success: function(html) {
                 // html is a string of all output of the server script.
                $("#element").html(html);
           }

        });
    }, 5000);
});
*/

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );


// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
//add_action( 'wp_ajax_nopriv_myajax-submit', 'myajax_submit' );
//add_action( 'wp_ajax_myajax-submit', 'myajax_submit' );


wp_localize_script( 'my-ajax-request', 'MyAjax', array(
    // URL to wp-admin/admin-ajax.php to process the request
    'ajaxurl'          => admin_url( 'admin-ajax.php' ),

    // generate a nonce with a unique ID "myajax-post-comment-nonce"
    // so that you can check it later when an AJAX request is sent
    'postCommentNonce' => wp_create_nonce( 'myajax-post-comment-nonce' ),
    )
);
	echo "<div id=\"phonelist\">loading...</div>";
}

function eesolve360_show_phonelist()
{
  
	global $wpdb;
   if ( is_user_logged_in() ) {
    $table_name = $wpdb->prefix. "solve360_contact";
	$sSQL = "SELECT * FROM $table_name ORDER BY name" ;
	$phonenumbers = $wpdb->get_results($sSQL, ARRAY_A);
	echo "<table border=\"1\" class=\"table table-striped\">
<thead>
  <tr>
    <th><b>Naam:</b></th>
    <th><b>Bedrijf:</b></th>
    <th><b>Telefoon:</b></th>
    <th><b>Mobiel:</b></th>


  </tr>
</thead>
<tbody>";
	foreach ( $phonenumbers as $record ) 
	{
			 echo "<tr>
				  <td>" . $record['name'] . "</td>
				  <td>" . $record['company'] . "</td>

				  <td><a href=\"?dial=" . cleanNumber($record['phone']) . "\">" . $record['phone'] ."</a></td>
				  <td><a href=\"?dial=" . cleanNumber($record['mobile']) ."\">" . $record['mobile'] . "</a></td>
				</tr>";
	}
	echo "</tbody></table>
	<a href=\"?update=true\" class=\"btn btn-primary\">Sync with Solve360</a>";
	}
}

function myajax_submit() {

    $nonce = $_POST['postCommentNonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! (wp_verify_nonce( $nonce, 'myajax-post-comment-nonce' ) )) die ( 'Busted!');

    // ignore the request if the current user doesn't have
    // sufficient permissions
    if (current_user_can('edit_posts' )) {
        // get the submitted parameters
        $postID = $_POST['postID'];

        // generate the response
        $response = json_encode( array( 'success' => true ) );

        // response output
        header( "Content-Type: application/json" );
        echo $response;
    }

    // IMPORTANT: don't forget to "exit"
    exit;
}
 function cleanupPhonenumber($sInput)
{
    $sInput = str_replace(array("-"," ","(",")"),array(""),$sInput);
    if (strlen($sInput) > 10) {
       if (substr($sInput,1,2) == "31") { $sInput =  0 . substr($sInput,3,strlen($sInput));}
       if (substr($sInput,1,3) == "+31") { $sInput =  0 . substr($sInput,4,strlen($sInput));}
       if (substr($sInput,1,4) == "0031") { $sInput =  0 . substr($sInput,5,strlen($sInput));}
    }
    if (strlen($sInput) == 11) {
         if (substr($sInput,1,1) == "1") {
            $sInput =  0 . substr($sInput,2,strlen($sInput));
         } else {
            $sInput =  substr($sInput,1,strlen($sInput));
         }
    }
    return $sInput;
}
function eesolve360_update_timetrack($solve360Service) 
{
	$result = $solve360Service->getTimeTracking($startDate, $endDate);
	foreach ($oResult->timerecords->timerecord as $timerecord)
	{
		$data = array(
		'id' => $timerecord->id,
		'type' => $timerecord->type,
		'parenttype' => $timerecord->parenttype,
		'itemid' => $timerecord->itemid,
		'itemtype' => $timerecord->itemtype,
		'parentparentid' => $timerecord->parentparentid,
		'parentparentcn' => $timerecord->parentparentcn,
		'parentid' => $timerecord->parentid,
		'parentcn' => $timerecord->parentcn,
		'owner' => $timerecord->owner,
		'created' => $timerecord->created,
		'details' => $timerecord->details,
		'billable' => $timerecord->billable,
		'hours' => $timerecord->hours,
		'person' => $timerecord->person,
		'date' => $timerecord->date,
		'itemname' => $timerecord->itemname
		);
		$result = $m->insert('user_data',$data);
		//echo var_dump($result);
	}
}
function eesolve360_update_contacts($solve360Service)
{
	global $wpdb;
     $table_name = $wpdb->prefix. "solve360_contact";

	if (isset($_GET['update'])) {
         $dbresult = $wpdb->query("TRUNCATE $table_name");
	 $oResult = $solve360Service->getContactFields("","");

	  foreach($oResult as $name => $data) {
	    	
	  $sPhone = $data->businessphonemain;
	  if ($sPhone == "") { $sPhone = $data->businessphonedirect; }

	   		if (!($data->name == "")) {


                //$sPhone = cleanupPhonenumber();
		   		$data_contact = array(
				'user_id' => (string)$data->id,
				'name' => (string)$data->name,
				'phone' => (string)cleanupPhonenumber($sPhone),
				'mobile' => (string)cleanupPhonenumber($data->cellularphone),
				'company' => (string)($data->company)
				);


				$dbresult = $wpdb->insert($wpdb->prefix. 'solve360_contact',$data_contact);
				// $wpdb->insert($wpdb->prefix. 'solve360_contact',$data_contact, array('%d','%s','%s','%s') %d = getal, %s = string
			   //	var_dump($dbresult);
			}
	    }
	    
	  $oResult = $solve360Service->getAllCompanies();

	  $table_comp_name = $wpdb->prefix. "solve360_company";
	  $dbresult = $wpdb->query("TRUNCATE $table_comp_name");
	  
	  foreach($oResult as $name => $data) {
	    	
	  $sPhone = $data->businessphonemain;
	  if ($sPhone == "") { $sPhone = $data->businessphonedirect; }

	   		if (!($data->name == "")) {


                //$sPhone = cleanupPhonenumber();
		   		$data_company = array(
					'company_id' => (string)$data->id,
					'name' => (string)$data->name,
					'phone' => (string)cleanupPhonenumber($sPhone),
					'mobile' => (string)cleanupPhonenumber($data->cellularphone)
				);

				$dbresult = $wpdb->insert($wpdb->prefix. 'solve360_company',$data_company);
				// $wpdb->insert($wpdb->prefix. 'solve360_contact',$data_contact, array('%d','%s','%s','%s') %d = getal, %s = string
			    // var_dump($dbresult);
			}
	    }
	   
	}
}
function eesolve360_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Solve360 settings</h2>
		<p>Please set settings below to use solve360
		</p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('eesolve360_plugin_options'); ?>
			<?php $options = get_option('eesolve360_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">
			
							<!-- Textbox Control -->
				<tr>
					<th scope="row">Enter Some Information</th>
					<td>
						<input type="text" size="57" name="eesolve360_options[txt_one]" value="<?php echo $options['txt_one']; ?>" />
					</td>
				</tr>

				</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php

}

// ------------------
// eesolve360_showresult will generate the (HTML) output
function eesolve360_showresult() {
global $wpdb;
// get your parameter values from the database
//$eesolve360_nb_widgets = get_option('eesolve360_nb_widgets');
echo "avbb";
// generate $eesolve360_result based on ...
// (your code)
// to do a query in the WP database, use the following line;
// $query = $wpdb->get_results("SELECT .. FROM $wpdb->.. WHERE .. ORDER BY ..");
// foreach ($query as $item) {
// (e.g.) $itemdate = $item->post_date_gmt;
// }
}
?>