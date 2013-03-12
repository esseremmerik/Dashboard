<?php

// This is how to include CSS files
function spark_enqueue_css() {

	// Include the CSS from the Spark Theme
	wp_enqueue_style('skeleton', get_template_directory_uri() . '/css/skeleton-v1.1.css', array(), '1.1', 'all');
	wp_enqueue_style('flexslider', get_template_directory_uri() . '/css/flexslider-v1.8.1.css', array(), '1.8.1', 'all');
	wp_enqueue_style('spark-main', get_template_directory_uri() . '/css/main-r8.css', array('skeleton', 'flexslider'), '8', 'all');
	wp_enqueue_style('media-queries', get_template_directory_uri() . '/css/media-queries-r7.css', array('skeleton'), '7', 'all');
	wp_enqueue_style('spark-theme-default', get_template_directory_uri() . '/css/theme-default-r6.css', array('spark-main'), '6', 'all');
	
	// Include "style.css" from this Child Theme:
	wp_enqueue_style('spark-child-theme', get_stylesheet_directory_uri() . '/style.css', array('spark-main','spark-theme-default'), null, 'all');
}


// This is how to include Javascript files
function spark_enqueue_js() {
	
	// Put jQuery at the bottom and load it from CDN
	wp_deregister_script('jquery');
	wp_register_script('jquery', "http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, '1.7.1', true);
	wp_enqueue_script('jquery');
	
	// Include the scripts from the Spark Theme
	wp_enqueue_script('flexslider', get_template_directory_uri() . '/js/jquery.flexslider-v1.8.min.js', array('jquery'), '1.8', true);
	wp_enqueue_script('hashchange', get_template_directory_uri() . '/js/jquery.ba-hashchange-v1.3.min.js', array('jquery'), '1.3', true);
	wp_enqueue_script('spark-main', get_template_directory_uri() . '/js/main-r9.js', array('jquery'), null, true);
	
	// If you want to add your own javascript file from this Child Theme:
	wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri() . '/scripts.js', array('jquery'), null, true);
}


// Tip: You can easily "override" most functions from the Spark Theme, 
// as most of them check for "if function_exists(..." before defining the function, 
// therefor you can copy/past functions (only those with a "function_exists()" check) from the Spark theme and edit them here.



