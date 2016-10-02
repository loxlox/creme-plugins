<?php

add_action( 'widgets_init', 'ar_widget_cat_register' );

function ar_widget_cat_register() {
	register_widget( 'ar_category_widget' );
}

class ar_category_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function ar_category_widget() {

		/* Create the widget. */
		WP_Widget::__construct( 
			'ar_category_widget', 
			esc_html__( 'Creme: Category Banner', 'creme-plugin' ), 
			array( 
				'classname'		=> 'ar_category_widget', 
				'description'	=> esc_html__( 'A widget that category with banner.', 'creme-plugin' ) 
			), 
			array( 
				'width'		=> 250, 
				'height'	=> 350, 
				'id_base'	=> 'ar_category_widget' 
			)
		);

	}

	/**
	 * Widget Form
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
        $defaults = array( 
        	'title'			=> 'Category Banner',
        	'post_shown'	=> '2',
        	'cat_inc'		=> ''
        );
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" />
        </p>

        <!-- Widget Post Count Shown: Number Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'post_shown' ) ); ?>"><?php esc_html_e( 'Category Count Shown:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="number" min="2" name="<?php echo esc_attr( $this->get_field_name( 'post_shown' ) ); ?>" value="<?php echo esc_attr( $instance['post_shown'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'post_shown' ) ); ?>" />
        </p>

        <!-- Widget Category Include: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'cat_inc' ) ); ?>"><?php esc_html_e( 'Category Include:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'cat_inc' ) ); ?>" value="<?php echo esc_attr( $instance['cat_inc'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'cat_inc' ) ); ?>" />
            <small><?php esc_html_e( 'Inset category id you want to show, separate with \' , \'. e.g. 2,3', 'creme-plugin' ) ?></small>
        </p>

<?php

	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] 	 	= strip_tags( $new_instance['title'] );
        $instance['post_shown'] = esc_attr( $new_instance['post_shown'] );
        $instance['cat_inc'] 	= esc_attr( $new_instance['cat_inc'] );

        return $instance;

	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title 		= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$cat_shown  = isset( $instance['post_shown'] ) ? esc_attr( $instance['post_shown'] ) : '2';
		$cat_inc  	= isset( $instance['cat_inc'] ) ? esc_attr( $instance['cat_inc'] ) : '';

		/* Before widget (defined by themes). */
		print( $before_widget );

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			print( $before_title . $title . $after_title );

		$categories = get_categories( array( 
			'number'	=> $cat_shown, 
			'include'	=> $cat_inc 
		) );
		$cat_count  = count( $categories );

		if ( $cat_count > 1 ) : ?>

			<div class="ar-cat-banner">

				<?php foreach ( $categories as $category ) : ?>

					<div class="cat-banner-<?php echo esc_attr( $category->term_id ); ?>">

						<div class="img-wrapper">

							<a href="<?php echo esc_url( get_category_link( $category->term_id ) ) ?>">

								<?php echo taxonomy_image( $category->term_id, 'creme-cat-banner' ); ?>

							</a>

						</div>

						<div class="title-wrapper">
							<h1 class="aligncenter">
								<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
							</h1>
						</div>

					</div>

				<?php endforeach; ?>

			</div>
		<?php
		endif;

		/* After widget (defined by themes). */
		print( $after_widget );

	}

}