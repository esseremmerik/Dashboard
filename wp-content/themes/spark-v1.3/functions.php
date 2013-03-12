<?php

// Spark

// Dev mode:
	//define('WP_DEBUG', true);
	//error_reporting(-1);

// Production mode:
	define('WP_DEBUG', false);
	error_reporting(0);

	
require_once(get_template_directory() . '/php/general.php');
require_once(get_template_directory() . '/php/theme-options.php');
require_once(get_template_directory() . '/php/plugins.php');

require_once(get_template_directory() . '/php/frontend-css.php');
require_once(get_template_directory() . '/php/frontend-js.php');

require_once(get_template_directory() . '/php/menus.php');
require_once(get_template_directory() . '/php/shortcodes.php');
//require_once(get_template_directory() . '/php/metaboxes.php'); // Not in use atm

