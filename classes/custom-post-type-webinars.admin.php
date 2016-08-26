<?php
/*
Plugin Name: Custom Post Type Webinars
Plugin URI: http://horttcore.de
Description: Custom Post Type Webinars
Version: 0.3
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/



/**
 *
 *  Custom Post Type Webinars
 *
 */
final class Custom_Post_Type_Webinars_Admin
{



	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function __construct()
	{

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'manage_webinar_posts_columns' , array( $this, 'manage_webinar_posts_columns' ) );
		add_action( 'manage_webinar_posts_custom_column' , array($this,'manage_webinar_posts_custom_column'), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'save_post', array( $this, 'save_webinar' ) );
		add_action( 'wp_ajax_get_webinar_lat_long', array( $this, 'ajax_get_webinar_lat_long' ) );

	} // END __construct



	/**
	 * Add meta boxes
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function add_meta_boxes()
	{

		add_meta_box( 'webinar-info', __( 'Webinar', 'custom-post-type-webinars' ), array( $this, 'meta_box_time' ), 'webinar' );

	} // END add_meta_boxes



	/**
	 * Register scripts
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function admin_enqueue_scripts()
	{

		wp_register_script( 'custom-post-type-webinars-admin', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/../scripts/custom-post-type-webinars.admin.js' ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), FALSE, TRUE );

	} // END admin_enqueue_scripts



	/**
	 * Add management columns
	 *
	 * @access public
	 * @param str $column Column name
	 * @param int $post_id Post ID
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function manage_webinar_posts_custom_column( $column, $post_id )
	{
		switch ( $column ) :

			case 'webinar-date' :

				the_webinar_date( FALSE, $post_id );

			break;

		endswitch;
	}



	/**
	 * Add management columns
	 *
	 * @access public
	 * @param array $columns Columns
	 * @return array
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function manage_webinar_posts_columns( $columns )
	{

		$columns['webinar-date'] = __( 'Webinar Date', 'custom-post-type-webinars' );
		return $columns;

	} // END manage_webinar_posts_columns



	/**
	 * Webinar info meta box
	 *
	 * @access public
	 * @param obj $post Post object
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function meta_box_time( $post )
	{

		wp_enqueue_script( 'custom-post-type-webinars-admin' );

		$info = get_post_meta( $post->ID, '_webinar-info', TRUE );
		$multiday = ( isset( $info['multi-day'] ) ) ? $info['multi-day'] : FALSE;
		$time = ( isset( $info['time'] ) ) ? $info['time'] : FALSE;
		$date_start = ( $timestamp = get_post_meta( $post->ID, '_webinar-date-start', TRUE ) ) ? absint( $timestamp ) : time();
		$date_end = ( $timestamp = get_post_meta( $post->ID, '_webinar-date-end', TRUE ) ) ? absint( $timestamp ) : time();
		$from_time = ( $timestamp = get_post_meta( $post->ID, '_webinar-time-start', TRUE ) ) ? absint( $timestamp ) : time();
		$to_time = ( $timestamp = get_post_meta( $post->ID, '_webinar-time-end', TRUE ) ) ? absint( $timestamp ) : time() + 3600;
		?>

		<table class="form-table">

			<tr>
				<th><label for="webinar-date"><?php _e( 'Date', 'custom-post-type-webinars'  ); ?></label></th>
				<td>
					<input type="text" name="webinar-date-start" id="webinar-date-start" value="<?php echo date_i18n( 'd.m.Y', $date_start ) ?>" /> <span class="multi-day">-
					<input type="text" name="webinar-date-end" id="webinar-date-end" value="<?php echo date_i18n( 'd.m.Y', $date_end ) ?>" /></span>
					<small><?php _e( 'DD.MM.YYYY', 'custom-post-type-webinars' ); ?></small><br>
				</td>
			</tr>

			<tr class="webinar-time">
				<th><label for="webinar-from-hour"><?php _e( 'Time', 'custom-post-type-webinars' ); ?></label></th>
				<td>
					<input type="text" name="webinar-from-hour" size="2" id="webinar-from-hour" value="<?php echo date_i18n( 'H', $from_time ) ?>" /> : <input type="text" size="2" name="webinar-from-minute" id="webinar-from-minute" value="<?php echo date_i18n( 'i', $from_time ) ?>" /> h -
					<input type="text" name="webinar-to-hour" size="2" id="webinar-to-hour" value="<?php echo date_i18n( 'H', $to_time ) ?>" /> : <input type="text" size="2" name="webinar-to-minute" id="webinar-to-minute" value="<?php echo date_i18n( 'i', $to_time ) ?>" /> h
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<label><input <?php checked( TRUE, $multiday ) ?> type="checkbox" name="webinar-multi-day" id="webinar-multi-day"> <?php _e( 'Multi-day', 'custom-post-type-webinars' ); ?></label>
					<label><input <?php checked( TRUE, $time ) ?> type="checkbox" name="webinar-time" id="webinar-time"> <?php _e( 'Time', 'custom-post-type-webinars' ); ?></label>
				</td>
			</tr>

		</table>

		<?php

		wp_nonce_field( 'save-webinar-info', 'webinar-info-nonce' );

	} // END meta_box_time



	/**
	 * Update messages
	 *
	 * @access public
	 * @param array $messages Messages
	 * @return array Messages
	 * @author Ralf Hortt
	 **/
	public function post_updated_messages( $messages )
	{

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( 'webinar' );

		$messages['webinar'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Webinar updated.', 'custom-post-type-webinars' ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Webinar updated.', 'custom-post-type-webinars' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Webinar restored to revision from %s', 'custom-post-type-webinars' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Webinar published.', 'custom-post-type-webinars' ),
			7  => __( 'Webinar saved.', 'custom-post-type-webinars' ),
			8  => __( 'Webinar submitted.', 'custom-post-type-webinars' ),
			9  => sprintf( __( 'Webinar scheduled for: <strong>%1$s</strong>.', 'custom-post-type-webinars' ), date_i18n( __( 'M j, Y @ G:i', 'custom-post-type-webinars' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Webinar draft updated.', 'custom-post-type-webinars' )
		);

		if ( $post_type_object->publicly_queryable ) :

			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View webinar', 'custom-post-type-webinars' ) );
			$messages[ 'webinar' ][1] .= $view_link;
			$messages[ 'webinar' ][6] .= $view_link;
			$messages[ 'webinar' ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview webinar', 'custom-post-type-webinars' ) );
			$messages[ 'webinar' ][8]  .= $preview_link;
			$messages[ 'webinar' ][10] .= $preview_link;

		endif;

		return $messages;

	} // END post_updated_messages



	/**
	 * Save post callback
	 *
	 * @access public
	 * @param int $post_id Post id
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function save_webinar( $post_id )
	{

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( is_multisite() && ms_is_switched() )
			return;

		if ( !isset( $_POST['webinar-info-nonce'] ) || !wp_verify_nonce( $_POST['webinar-info-nonce'], 'save-webinar-info' ) )
			return;

		// Info
		$multiday = ( isset( $_POST['webinar-multi-day'] ) ) ? TRUE : FALSE;
		$hastime = ( isset( $_POST['webinar-time'] ) ) ? TRUE : FALSE;
		update_post_meta( $post_id, '_webinar-info', array(
			'multi-day' => $multiday,
			'time' => $hastime,
		) );

		// Delete unused stuff
		if ( '' == $_POST['webinar-date-start'] ) :
			delete_post_meta( $post_id, '_webinar-date-start' );
			delete_post_meta( $post_id, '_webinar-date-end' );
			delete_post_meta( $post_id, '_webinar-time-start' );
			delete_post_meta( $post_id, '_webinar-time-end' );
			return;
		endif;

		// Webinar date
		$date_start = explode( '.', $_POST['webinar-date-start'] );
		$date_start = mktime( 0, 0, 0, $date_start[1], $date_start[0], $date_start[2] );
		update_post_meta( $post_id, '_webinar-date-start', $date_start );

		if ( isset( $_POST['webinar-date-end'] ) && '' != $_POST['webinar-date-end'] ) :
			$date_end = ( $_POST['webinar-date-end'] ) ? explode( '.', $_POST['webinar-date-end'] ) : $date_start;
			$date_end = mktime( 23, 59, 59, $date_end[1], $date_end[0], $date_end[2] );
			update_post_meta( $post_id, '_webinar-date-end', $date_end );
		else :
			$date_end = mktime( 23, 59, 59, date_i18n('m', $date_start), date_i18n('d', $date_start), date_i18n('Y', $date_start) );
			update_post_meta( $post_id, '_webinar-date-end', $date_end );
		endif;

		// Webinar time
		if ( $_POST['webinar-from-hour'] && $_POST['webinar-from-minute'] ) :
			$date_start = explode( '.', $_POST['webinar-date-start'] );
			$date_start = mktime( $_POST['webinar-from-hour'], $_POST['webinar-from-minute'], 0, $date_start[1], $date_start[0], $date_start[2] );
			update_post_meta( $post_id, '_webinar-time-start', $date_start );
		else :
			delete_post_meta( $post_id, '_webinar-time-start' );
		endif;

		if ( $_POST['webinar-to-hour'] && $_POST['webinar-to-minute'] ) :
			$date_end = ( $_POST['webinar-date-end'] ) ? explode( '.', $_POST['webinar-date-end'] ) : explode( '.', $_POST['webinar-date-start'] );
			$date_end = mktime( $_POST['webinar-to-hour'], $_POST['webinar-to-minute'], 0, $date_end[1], $date_end[0], $date_end[2] );
			update_post_meta( $post_id, '_webinar-time-end', $date_end );
		else :
			delete_post_meta( $post_id, '_webinar-time-end' );
		endif;

	} // END save_webinar



} // END final class Custom_Post_Type_Webinars_Admin

new Custom_Post_Type_Webinars_Admin;
