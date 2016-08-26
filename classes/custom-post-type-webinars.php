<?php
/*
Plugin Name: Custom Post Type Webinars
Plugin URI: http://horttcore.de
Description: Custom Post Type Webinars
Version: 1.0
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/



/**
 *
 *  Custom Post Type Webinars
 *
 */
final class Custom_Post_Type_Webinars
{



	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt
	 **/
	public function __construct()
	{

		add_action( 'custom-post-type-webinars-widget-output', 'Custom_Post_Type_Webinars_Widget::widget_output', 10, 3 );
		add_action( 'custom-post-type-webinars-widget-loop-output', 'Custom_Post_Type_Webinars_Widget::widget_loop_output', 10, 3 );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'page_rewrite_rules', array( $this, 'rewrite_rules' ) );
		add_filter( 'widgets_init', array( $this, 'widgets_init' ) );


	} // END __construct



	/**
	 * Theme query vars
	 *
	 * @param array $vars Query variables
	 * @return array Query variables
	 * @since v2.0
	 * @author Ralf Hortt
	 **/
	public function query_vars( $vars )
	{

	    $add = array(
			'webinar-year',
			'webinar-month',
			'webinar-day',
	    );

	    return array_merge( $add, $vars );

	} // END query_vars



	/**
	 * Load plugin translation
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v1.0.0
	 **/
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain( 'custom-post-type-webinars', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/'  );

	} // END load_plugin_textdomain



	/**
	 * Reorder webinar archive
	 *
	 * @access public
	 * @param obj $query WP_Query object
	 * @return void
	 * @since v0.1
	 * @author Ralf Hortt
	 **/
	public function pre_get_posts( $query )
	{

		if ( !$query->is_main_query() || is_admin() )
			return;

		if ( !is_post_type_archive( 'webinar' ) && !is_tax( 'webinar-category') )
			return;

		if ( TRUE == apply_filters( 'remove-past-webinars-from-loop', TRUE ) )
			$query->set( 'order', 'ASC' );
		else
			$query->set( 'order', 'DESC' );

		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_webinar-date-start' );

		if ( get_query_var( 'webinar-year' ) && get_query_var( 'webinar-month' ) && get_query_var( 'webinar-day' ) ) :

			$query->set( 'meta_query', array(
				array(
					'key' => '_webinar-date-start',
					'value' => mktime( 12, 0, 0, get_query_var( 'webinar-month' ), get_query_var( 'webinar-day' ), get_query_var( 'webinar-year' ) ),
					'compare' => '<=',
					'type' => 'numeric',
				),
				array(
					'key' => '_webinar-date-end',
					'value' => mktime( 12, 0, 0, get_query_var( 'webinar-month' ), get_query_var( 'webinar-day' ), get_query_var( 'webinar-year' ) ),
					'compare' => '>=',
					'type' => 'numeric',
				)
			));

		elseif ( get_query_var( 'webinar-year' ) && get_query_var( 'webinar-month' ) ) :

			$query->set( 'meta_query', array(
				array(
					'key' => '_webinar-date-end',
					'value' => array( mktime( 0, 0, 0, get_query_var( 'webinar-month' ), 1, get_query_var( 'webinar-year' ) ), mktime( 0, 0, 0, get_query_var( 'webinar-month' ), date( 't', mktime( 0, 0, 0, get_query_var( 'webinar-month' ), 1, get_query_var( 'webinar-year' )) ), get_query_var( 'webinar-year' ) ) ),
					'compare' => 'BETWEEN',
					'type' => 'numeric'
				)
			));

		elseif ( get_query_var( 'webinar-year' ) ) :

			$query->set( 'meta_query', array(
				array(
					'key' => '_webinar-date-end',
					'value' => array( mktime( 0, 0, 0, 1, 1, get_query_var( 'webinar-year' ) ), mktime( 0, 0, 0, 12, 31, get_query_var( 'webinar-year' ) ) ),
					'compare' => 'BETWEEN',
					'type' => 'numeric'
				),
			));

		else :

			$query->set( 'meta_query', array(
				array(
					'key' => '_webinar-date-end',
					'value' => time(),
					'compare' => '>=',
					'type' => 'NUMERIC'
				)
			) );

		endif;

	} // END pre_get_posts



	/**
	 * Register post type
	 *
	 * @access public
	 * @return void
	 * @since v0.1
	 * @author Ralf Hortt
	 */
	public function register_post_type()
	{

		register_post_type( 'webinar', array(
			'labels' => array(
				'name' => _x( 'Webinars', 'post type general name', 'custom-post-type-webinars' ),
				'singular_name' => _x( 'Webinar', 'post type singular name', 'custom-post-type-webinars' ),
				'add_new' => _x( 'Add New', 'Webinar', 'custom-post-type-webinars' ),
				'add_new_item' => __( 'Add New Webinar', 'custom-post-type-webinars' ),
				'edit_item' => __( 'Edit Webinar', 'custom-post-type-webinars' ),
				'new_item' => __( 'New Webinar', 'custom-post-type-webinars' ),
				'view_item' => __( 'View Webinar', 'custom-post-type-webinars' ),
				'search_items' => __( 'Search Webinar', 'custom-post-type-webinars' ),
				'not_found' =>  __( 'No Webinars found', 'custom-post-type-webinars' ),
				'not_found_in_trash' => __( 'No Webinars found in Trash', 'custom-post-type-webinars' ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Webinars', 'custom-post-type-webinars' )
			),
			'public' => TRUE,
			'publicly_queryable' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array(
				'slug' => _x( 'webinars', 'Post Type Slug', 'custom-post-type-webinars' ),
				'with_front' => FALSE,
			),
			'capability_type' => 'post',
			'has_archive' => FALSE,
			'hierarchical' => FALSE,
			'menu_position' => NULL,
			'menu_icon' => 'dashicons-format-video',
			'supports' => array( 'title', 'editor', 'thumbnail' )
		));

	} // END register_post_type



	/**
	 * Theme rewrite rules
	 *
	 * @access public
	 * @param array $rules Rewrite rules
	 * @return array Rewrite rules
	 * @author Ralf Hortt
	 **/
	public function rewrite_rules( $rules )
	{

	    global $wp_rewrite;

		return array_merge( array(
				_x( 'webinars', 'Post Type Slug', 'custom-post-type-webinars' ) . '/(.+)/(.+)/(.+)/?$' => 'index.php?post_type=webinar&webinar-year=$matches[1]&webinar-month=$matches[2]&webinar-day=$matches[3]',
				_x( 'webinars', 'Post Type Slug', 'custom-post-type-webinars' ) . '/(.+)/(.+)/?$' => 'index.php?post_type=webinar&webinar-year=$matches[1]&webinar-month=$matches[2]',
				_x( 'webinars', 'Post Type Slug', 'custom-post-type-webinars' ) . '/(.+)/?$' => 'index.php?post_type=webinar&webinar-year=$matches[1]',
			),
		$rules );

	} // END theme_rewrite_rules



	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function widgets_init()
	{

		register_widget( 'Custom_Post_Type_Webinars_Widget' );

	}



}

new Custom_Post_Type_Webinars;
