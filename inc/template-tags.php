<?php
if ( !function_exists( 'get_webinar_date' ) ) :
/**
 * Get webinar location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_webinar_info( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$info = get_post_meta( $post_id, '_webinar-info', TRUE );
	$start = get_post_meta( $post_id, '_webinar-date-start', TRUE );
	$end = get_post_meta( $post_id, '_webinar-date-end', TRUE );

	$time_start = ( TRUE === $info['time'] ) ? get_post_meta( $post_id, '_webinar-time-start', TRUE ) : FALSE;
	$time_end = ( TRUE === $info['time'] ) ? get_post_meta( $post_id, '_webinar-time-end', TRUE ) : FALSE;

	return array(
		'multi-day' => $info['multi-day'],
		'time' => $info['time'],
		'start' => $start,
		'time-start' => $time_start,
		'end' => $end,
		'time-end' => $time_end,
	);

}
endif;



if ( !function_exists( 'get_webinar_datetime' ) ) :
/**
 * Get webinar location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_webinar_datetime( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$webinar = get_webinar_info( $post_id );
	$output = get_webinar_date( $format, $post_id );

	if ( $webinar['time'] )
		$output .= apply_filters( 'webinar_datetime_seperator', ' | ' ) . get_webinar_time();

	return $output;

}
endif;



if ( !function_exists( 'the_webinar_datetime' ) ) :
/**
 * Get webinar location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_webinar_datetime( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_webinar_datetime( $format, $post_id );

}
endif;



if ( !function_exists( 'has_webinar_date' ) ) :
/**
 * Conditional if webinar location is set
 *
 * @param int $post_id Post ID
 * @return bool
 * @author Ralf Hortt
 **/
function has_webinar_date( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();
	$data = get_webinar_info( $post_id );

	return ( $data['start'] ) ? TRUE : FALSE;

}
endif;



if ( !function_exists( 'get_webinar_date' ) ) :
/**
 * Get webinar location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_webinar_date( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$webinar = get_webinar_info( $post_id );

	if ( $webinar['multi-day'] )
		$output = sprintf( '%s - %s', date_i18n( $format, $webinar['start'] ), date_i18n( $format, $webinar['end'] ) );
	else
		$output = date_i18n( $format, $webinar['start'] );

	return $output;

}
endif;



if ( !function_exists( 'the_webinar_date' ) ) :
/**
 * Get webinar location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_webinar_date( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_webinar_date( $format, $post_id );

}
endif;



if ( !function_exists( 'has_webinar_time' ) ) :
/**
 * Conditional if webinar time is set
 *
 * @param int $post_id Post ID
 * @return bool
 * @author Ralf Hortt
 **/
function has_webinar_time( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();
	return ( '' !== get_webinar_time( 'H:i', $post_id ) ) ? TRUE : FALSE;

}
endif;



if ( !function_exists( 'get_webinar_time' ) ) :
/**
 * Get webinar time
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_webinar_time( $format = 'H:i', $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$webinar = get_webinar_info();

	if ( !$webinar['time'] )
		return '';

	if ( $webinar['time-end'] )
		return sprintf( _x( '%sh - %sh', 'Time range', 'custom-post-type-webinars' ), date_i18n( $format, $webinar['time-start'] ), date_i18n( $format, $webinar['time-end'] ) );
	else
		return sprintf( _x( '%sh', 'Singe time', 'custom-post-type-webinars' ), date_i18n( 'H:i', $webinar['time-start'] ) );

}
endif;



if ( !function_exists( 'the_webinar_time' ) ) :
/**
 * Print webinar time
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_webinar_time( $format = 'H:i', $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_webinar_time( $format, $post_id );

}
endif;
