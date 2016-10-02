<?php

/**
 * Plugin Name: About Widget
 */
add_action( 'widgets_init', 'ar_widget_about_register' );

function ar_widget_about_register() {
	register_widget( 'ar_widget_about' );
}

class ar_widget_about extends WP_Widget {

	/**
	 * Widget Setup
	 */
	function ar_widget_about() {

		WP_Widget::__construct( 
			'ar_widget_about', 
			esc_html__( 'Creme: About', 'creme-plugin' ), 
			array( 
				'classname'		=> 'ar_widget_about', 
				'description'	=> esc_html__( 'A widget that displays an About Owner of Site.', 'creme-plugin' ) 
			), 
			array( 
				'width'		=> 250, 
				'height'	=> 350, 
				'id_base'	=> 'ar_widget_about' 
			)
		);

	}

	/**
	 * Widget Form
	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults   = array( 
			'title'			=> 'About Me', 
			'image'			=> '', 
			'description'	=> '', 
			'social_acc'	=> true 
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Title -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'creme-plugin' ); ?></label><br />
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>

		<!-- Image Url -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image URL', 'creme-plugin' ); ?></label><br />
			<input class="widefat" type="url" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" value="<?php echo esc_url( $instance['image'] ); ?>"/><br />
			<small><?php esc_html_e( 'Insert your image URL. For best result use 300px width', 'creme-plugin' ); ?></small>
		</p>

		<!-- Description -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Description', 'creme-plugin' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" style="width:100%;" rows="6"><?php echo wp_kses_post( $instance['description'] ); ?></textarea>
		</p>

		<!-- Social Account -->
		<p>
	        <input class="checkbox" type="checkbox" <?php checked( (bool)$instance['social_acc'] ); ?> id="<?php echo esc_attr( $this->get_field_id( 'social_acc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'social_acc' ) ); ?>" />
	        <label for="<?php echo esc_attr( $this->get_field_id( 'social_acc' ) ); ?>"><?php esc_html_e( 'Enable Social Icon', 'creme-plugin' ); ?></label><br />
			<small><?php esc_html_e( 'You can insert url Social Account in Customize > Social Account.', 'creme-plugin' ); ?></small>
        </p>

<?php

	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 		 = esc_attr( $new_instance['title'] );
		$instance['image'] 		 = esc_url( $new_instance['image'] );
		$instance['description'] = do_shortcode( $new_instance['description'] );
		$instance['social_acc']  = esc_attr( $new_instance['social_acc'] );

		return $instance;

	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {

		extract( $args );

		/* Our variables from the widget settings. */
		$title 		 = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$image 		 = isset( $instance['image'] ) ? esc_url( $instance['image'] ) : '';
		$description = isset( $instance['description'] ) ? do_shortcode( $instance['description'] ) : '';
		$social_acc  = isset( $instance['social_acc'] ) ? esc_attr( $instance['social_acc'] ) : '';

		/* Before widget (defined by themes). */
		print( $before_widget );

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			print( $before_title . $title . $after_title );
		?>
		<div class="ar-widget-about">
			<img class="ar-avatar" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
			<p><?php echo do_shortcode( $description ); ?></p>
			<?php if( $social_acc ) {
				ar_social_acc();
			} ?>
			<div class="clear"></div>
		</div>

		<?php
		/* After widget (defined by themes). */
		print( $after_widget );

	}

}