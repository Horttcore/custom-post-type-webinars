<?php
/**
 * Plugin Name: Custom Post Type Webinars
 * Plugin URI: http://horttcore.de
 * Description: Manage webinars
 * Version: 2.0
 * Author: Ralf Hortt
 * Author URI: https://horttcore.de
 * Text Domain: custom-post-type-webinars
 * Domain Path: /languages/
 * License: GPL2
 */

require( 'classes/custom-post-type-webinars.php' );
require( 'classes/custom-post-type-webinars.widget.php' );
require( 'inc/template-tags.php' );

if ( is_admin() )
	require( 'classes/custom-post-type-webinars.admin.php' );
