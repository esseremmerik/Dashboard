<?php
/*
Plugin Name: Solve360 for WordPress
Plugin URI: http://www.garmantech.com/wordpress-plugins/solve360/
Description: Add some lead capturing powers to your WordPress site.
Version: 1.0
Author: Garman Technical Services
Author URI: http://www.garmantech.com/wordpress-plugins/
License: GPLv2
*/

/*  Copyright 2012  Garman Technical Services  (email : contact@garmantech.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// If class Solve360 already exists, return and let's not procede. That would cause errors.
if (!class_exists('Solve360')) return;

// The class is useless if we do not start it, when plugins are loaded let's start the class.
add_action ('plugins_loaded', 'Solve360');
function Solve360() { $Solve360 = new Solve360; }

class Solve360 {
	function __construct() {
		// We need to require the Solve360 API Library
		require_once('Solve360Service.php');
		
		// We need an options page
	//	add_options_page('Solve360 Options', 'Solve360', 'manage_options', 'solve360', array(&$this,'options_page'));
		add_action('admin_init',array(&$this,'settings_init'));
		
		// Now tell WordPress to use our code by adding some actions
		add_action('widgets_init', create_function('', 'register_widget("Solve360Widget");'));
		add_action('init', array(&$this, 'solve360post'));
		
		// Some default styles for Solve360
		wp_register_style('solve360css',plugins_url('solve360.css', __FILE__));
		wp_enqueue_style('solve360css');
		
		// Not that there is anything in the languagnes folder, but let's load some translations
		load_plugin_textdomain('solve360', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	function settings_init() {
		$settings = array(
			array(
				'name'		=> 'solve360_api_info',
				'title' 	=> __('Your Solve360 API Credentials','solve360'),
				'page'		=> 'general',
				'callback'	=> array(&$this,'settings_section_api_info'),
				'settings'	=> array(
					array(
						'name'		=> 'solve360_api_info_user',
						'title'		=> __('User Email','solve360'),
						'callback'	=> array(&$this,'settings_field_user'),
					),
					array(
						'name'		=> 'solve360_api_info_token',
						'title'		=> __('API Token','solve360'),
						'callback'	=> array(&$this,'settings_field_token'),
					),
					array(
						'name'		=> 'solve360_api_info_notifications',
						'title'		=> __('Notifications Email','solve360'),
						'callback'	=> array(&$this,'settings_field_notifications'),
					),
					array(
						'name'		=> 'solve360_api_info_thankyou',
						'title'		=> __('Thank You URL','solve360'),
						'callback'	=> array(&$this,'settings_field_thankyou'),
					),
					array(
						'name'		=> 'solve360_api_info_error',
						'title'		=> __('Error URL','solve360'),
						'callback'	=> array(&$this,'settings_field_error'),
					),
				),
			),
		);
		
		// My super secret settings array into settings form-ifier
		foreach($settings as $sections=>$section) {
			add_settings_section($section['name'],$section['title'],$section['callback'],$section['page']);
			foreach($section['settings'] as $setting=>$option) {
				add_settings_field($option['name'],$option['title'],$option['callback'],$section['page'],$section['name']);
				register_setting($section['page'],$option['name']);
			}
		}
	}
	
	function settings_section_api_info() { echo '<p>'.__('You can find your API token by logging into Solve360 and checking the "My Account" window.','solve360').'</p>'; }
	function settings_field_user() { echo '<input type="text" name="solve360_api_info_user" id="solve360_api_info_user" value="'.get_option('solve360_api_info_user').'" />'; }
	function settings_field_token() { echo '<input type="password" name="solve360_api_info_token" id="solve360_api_info_token" value="'.get_option('solve360_api_info_token').'" />'; }
	function settings_field_notifications() { echo '<input type="text" name="solve360_api_info_notifications" id="solve360_api_info_notifications" value="'.get_option('solve360_api_info_notifications').'" />'; }
	function settings_field_thankyou() { echo '<input type="text" name="solve360_api_info_thankyou" id="solve360_api_info_thankyou" value="'.get_option('solve360_api_info_thankyou').'" />'; }
	function settings_field_error() { echo '<input type="text" name="solve360_api_info_error" id="solve360_api_info_error" value="'.get_option('solve360_api_info_error').'" />'; }
	
	function solve360post() {
		if(isset($_GET['solve360post']) && $_GET['solve360post'] == 1) {
			foreach($_POST as $key=>$value) { $data[$key] = trim($value); }
			$api = new Solve360Service(get_option('solve360_api_info_user'),get_option('solve360_api_info_token'));
			
			$fields = array('firstname','lastname','businessemail');
			foreach($fields as $field) {
				$data[$field] = trim($_POST[$field]);
			}
			$data['ownership'] = $_POST['ownership'];
			$data['categories'] = array('add' => array('category' => explode(',',$_POST['categories'])));
			
			$contacts = $api->searchContacts(array(
				'filtermode' => 'byemail',
				'filtervalue' => $data['businessemail'],
			));
			
			if ($contacts->count > 0) {
				$action = 'update';
				$contactId = current($contacts->children())->id;
				$contactName = current($contacts->children())->name;
				$contact = $api->editContact($contactId, $data);
			} else {
				$action = 'new';
				$contact = $api->addContact($data);
				$contactName = $contact->item->name;
				$contactId   = $contact->item->id;        
			}
			
			$format = ($action == 'new') ? __('Contact "%s" (%d) was generated using <a href="http://www.garmantech.com/wordpress-plugins/solve360/">Solve360 for WordPress</a>','solve360') : __('Contact "%s" (%d) was updated using <a href="http://www.garmantech.com/wordpress-plugins/solve360/">Solve360 for WordPress</a>','solve360');
			$addnote = $api->addActivity($contactId, 'note', array('details' => nl2br(sprintf($format, $contactName, $contactId))));

			if(isset($contact->errors)) {
				if((get_option('solve360_api_info_notifications')!='')) {
					wp_mail(get_option('solve360_api_info_notifications'), __('Error Posting Solve360 Lead','solve360'), $contact->errors->asXml());
				}
				header('Location:'.get_option('solve360_api_info_error'));
			} else {
				if((get_option('solve360_api_info_notifications')!='')) {
					$subject = ($action == 'new') ?  __('New Solve360 Lead','solve360') :  __('Updated Solve360 Lead','solve360');
					$message = ($action == 'new') ? sprintf('Contact "%s" was posted to Solve360. https://secure.solve360.com/contact/%d',$contactName,$contactId) : sprintf('Contact "%s" was updated on Solve360. https://secure.solve360.com/contact/%d',$contactName,$contactId);
					wp_mail(get_option('solve360_api_info_notifications'), $subject, $message);
				}
				header('Location:'.get_option('solve360_api_info_thankyou'));
			}
		}
	}
	
	// We need a shortcode
	
}

class Solve360Widget extends WP_Widget {

	function __construct() {
		parent::WP_Widget( /* Base ID */'solve360widget', /* Name */'Solve360 Leads', array( 'description' => 'Adds a lead capturing form to your website powered by Solve360.' ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		echo '<form action="'.trailingslashit(home_url()).'?solve360post=1&source=widget" method="POST">';
			echo '<label>'.__('First Name','solve360').'</label><input type="text" name="firstname" value="" />';
			echo '<label>'.__('Last Name','solve360').'</label><input type="text" name="lastname" value="" />';
			echo '<label>'.__('Email','solve360').'</label><input type="text" name="businessemail" value="" />';
			echo '<input type="hidden" name="ownership" value="'.$instance['ownership'].'" />';
			echo '<input type="hidden" name="categories" value="'.$instance['categories'].'" />';
      		echo '<input type="submit" value="'.__('Submit','solve360').'" />';
		echo '</form>';
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new, $old ) {
		$instance = $old;
		$instance['title'] = trim($new['title']);
		$instance['ownership'] = trim($new['ownership']);
		$categories = explode(',',$new['categories']);
		foreach($categories as $category) if(trim($category)!='') $cats[] = trim($category);
		$instance['categories'] = implode(',',$cats);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) { ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('ownership'); ?>"><?php _e('Ownership:'); ?></label> 
			<input id="<?php echo $this->get_field_id('ownership'); ?>" type="text" name="<?php echo $this->get_field_name('ownership'); ?>" value="<?php echo $instance['ownership']; ?>" />
			<i>User ID for ownership</i>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label> 
			<input id="<?php echo $this->get_field_id('categories'); ?>" type="text" name="<?php echo $this->get_field_name('categories'); ?>" value="<?php echo $instance['categories']; ?>" />
			<i>CSV list of categories</i>
		</p>
	<?php }

}