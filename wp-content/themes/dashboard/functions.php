<?php
wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap.min.css',false,'1.1','all');
wp_enqueue_style('bootstrap-responsive', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap-responsive.min.css',false,'1.1','all');

wp_enqueue_style('bootstrap-jasny', get_stylesheet_directory_uri() . '/bootstrap/extend/jasny-bootstrap/css/jasny-bootstrap.min.css',false,'1.1','all');
wp_enqueue_style('bootstrap-jasnyresponsive', get_stylesheet_directory_uri() . '/bootstrap/extend/jasny-bootstrap/css/jasny-bootstrap-responsive.min.css',false,'1.1','all');
wp_enqueue_style('bootstrap-html5', get_stylesheet_directory_uri() . '/bootstrap/extend/bootstrap-wysihtml5/css/bootstrap-wysihtml5-0.0.2.css',false,'1.1','all');

wp_enqueue_style('themecss', get_stylesheet_directory_uri() . '/theme/css/style.min.css',false,'1.1','all');
wp_enqueue_style('themejqueryui', get_stylesheet_directory_uri() . '/theme/scripts/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css',false,'1.1','all');
wp_enqueue_style('themeglyphicons', get_stylesheet_directory_uri() . '/theme/css/glyphicons.css',false,'1.1','all');

wp_enqueue_style('extend-select', get_stylesheet_directory_uri() . '/bootstrap/extend/bootstrap-select/bootstrap-select.css',false,'1.1','all');
wp_enqueue_style('extend-buttons', get_stylesheet_directory_uri() . '/bootstrap/extend/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css',false,'1.1','all');
wp_enqueue_style('uniform', get_stylesheet_directory_uri() . '/theme/scripts/pixelmatrix-uniform/css/uniform.default.css',false,'1.1','all');
wp_enqueue_script('modernizr',get_stylesheet_directory_uri() . '/theme/scripts/modernizr.custom.76094.js');
wp_enqueue_script('less',get_stylesheet_directory_uri() . '/theme/scripts/less-1.3.3.min.js');


?>