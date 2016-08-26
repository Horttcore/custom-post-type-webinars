<?php
/**
 * Widget
 *
 * @author Ralf Hortt
 */
if ( !class_exists( 'Custom_Post_Type_Webinars_Widget' ) ) :
class Custom_Post_Type_Webinars_Widget extends WP_Widget {



	/**
	 * Constructor
	 *
	 * @access public
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function __construct()
	{

		$widget_ops = array(
			'classname' => 'widget-webinars',
			'description' => __( 'Lists the latest webinars', 'custom-post-type-webinars' ),
		);
		$control_ops = array( 'id_base' => 'widget-webinars' );
		parent::__construct( 'widget-webinars', __( 'Webinars', 'custom-post-type-webinars' ), $widget_ops, $control_ops );

	} // END __construct



	/**
	 * Output
	 *
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function widget( $args, $instance ) {

		$query = array(
			'post_type' => 'webinar',
			'showposts' => $instance['limit'],
			'orderby' => $instance['orderby'],
			'order' => $instance['order'],
		);

		if ( 0 != $instance['webinar-category'] ) :
			$query['tax_query'] = array(
				array(
					'taxonomy' => 'webinar-category',
					'field' => 'term_id',
					'terms' => $instance['webinar-category'],
				)
			);
		endif;

		if ( 'webinar-date' == $instance['orderby'] ) :
			$query['orderby'] = 'meta_value_num';
			$query['meta_query'][] = array(
				'key' => '_webinar-date-end',
				'value' => time(),
				'compare' => '>=',
				'type' => 'NUMERIC'
			);
		endif;

		$query = new WP_Query( $query );

		if ( $query->have_posts() ) :

			/**
			 * Widget output
			 *
			 * @param array $args Arguments
			 * @param array $instance Widget instance
			 * @param obj $query WP_Query object
			 * @hooked Custom_Post_Type_Widget::widget_output - 10
			 */
			do_action( 'custom-post-type-webinars-widget-output', $args, $instance, $query );

		endif;

		wp_reset_query();

	} // END widget



	/**
	 * Save widget settings
	 *
	 * @access public
	 * @param array $new_instance New widget instance
	 * @param array $old_instance Old widget instance
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function update( $new_instance, $old_instance )
	{

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['orderby'] = $new_instance['orderby'];
		$instance['order'] = $new_instance['order'];
		$instance['limit'] = $new_instance['limit'];

		$instance['webinar-category'] = ( isset( $new_instance['webinar-category'] ) ) ? $new_instance['webinar-category'] : FALSE;

		return $instance;

	} // END update



	/**
	 * Widget settings
	 *
	 * @access public
	 * @param array $instance Widget instance
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v2.0
	 */
	public function form( $instance )
	{

		?>

		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label><br>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if ( isset( $instance['title'] ) ) echo esc_attr( $instance['title'] ) ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'orderby' ); ?>"><?php _e( 'Order By:', 'custom-post-type-webinars' ); ?></label><br>
			<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<option <?php selected( $instance['orderby'], '' ) ?> value=""><?php _e( 'None' ); ?></option>
				<option <?php selected( $instance['orderby'], 'webinar-date' ) ?> value="webinar-date"><?php _e( 'Event date' ); ?></option>
				<option <?php selected( $instance['orderby'], 'ID' ) ?> value="ID"><?php _e( 'ID', 'custom-post-type-webinars' ); ?></option>
				<option <?php selected( $instance['orderby'], 'title' ) ?> value="title"><?php _e( 'Title' ); ?></option>
				<option <?php selected( $instance['orderby'], 'date' ) ?> value="date"><?php _e( 'Publishing date' ); ?></option>
				<option <?php selected( $instance['orderby'], 'rand' ) ?> value="rand"><?php _e( 'Random' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'order' ); ?>"><?php _e( 'Order:' ); ?></label><br>
			<select name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_name( 'order' ); ?>">
				<option <?php selected( $instance['order'], 'ASC') ?> value="ASC"><?php _e( 'Ascending', 'custom-post-type-webinars' ); ?></option>
				<option <?php selected( $instance['order'], 'DESC') ?> value="DESC"><?php _e( 'Descending', 'custom-post-type-webinars' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'limit' ); ?>"><?php _e( 'Count:', 'custom-post-type-webinars' ); ?></label><br>
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php if ( isset( $instance['limit'] ) )  echo esc_attr( $instance['limit'] ) ?>">
		</p>

		<?php
		$category_dropdown = wp_dropdown_categories(array(
			'show_option_all' => __( 'All', 'custom-post-type-webinars' ),
			'taxonomy' => 'webinar-category',
			'name' => $this->get_field_name( 'webinar-category' ),
			'selected' => $instance['webinar-category'],
			'hide_if_empty' => TRUE,
			'hide_empty' => FALSE,
			'hierarchical' => TRUE,
			'echo' => FALSE
		));

		if ( $category_dropdown ) :

			?>

			<p>
				<label for="<?php echo $this->get_field_name( 'webinar-category' ); ?>"><?php _e( 'Category' ); ?></label><br>
				<?php echo $category_dropdown ?>
			</p>

			<?php

		endif;

	} // END form



	/**
	 * Widget loop output
	 *
	 * @static
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @param obj $query WP_Query object
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v2.0
	 **/
	static public function widget_loop_output( $args, $instance, $query )
	{

		?>

		<li>
			<a href="<?php the_permalink() ?>"><span><?php the_webinar_date() ?></span><br><?php the_title() ?></a>
		</li>

		<?php

	} // END widget_loop_output



	/**
	 * Widget output
	 *
	 * @static
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @param obj $query WP_Query object
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v2.0
	 **/
	static public function widget_output( $args, $instance, $query )
	{

		echo $args['before_widget'];

		echo $args['before_title'] . $instance['title'] . $args['after_title'];

		?>

		<ul class="webinar-list">

			<?php

			while ( $query->have_posts() ) : $query->the_post();

				/**
				 * Loop output
				 *
				 * @param array $args Arguments
				 * @param array $instance Widget instance
				 * @param obj $query WP_Query object
				 * @hooked Custom_Post_Type::widget_loop_output - 10
				 */
				do_action( 'custom-post-type-webinars-widget-loop-output', $args, $instance, $query );

			endwhile;

			?>

		</ul>

		<?php

		echo $args['after_widget'];

	} // END widget_output



} // END final class Custom_Post_Type_Webinars_Widget

endif;
