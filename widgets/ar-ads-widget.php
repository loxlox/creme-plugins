<?php

/**
 * Plugin Name: Ads Widget
 */
add_action( 'widgets_init', 'ar_widget_adspace_register' );

function ar_widget_adspace_register() {
	register_widget( 'ar_ads_widget' );
}

class ar_ads_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function ar_ads_widget() {

		/* Create the widget. */
		WP_Widget::__construct( 
            'ar_ads_widget', 
            esc_html__( 'Creme: Adspace', 'creme-plugin' ), 
            array( 
                'classname'     => 'ar_ads_widget', 
                'description'   => esc_html__( 'A widget that displays adspace.', 'creme-plugin' )
            ), 
            array( 
                'width'     => 250, 
                'height'    => 350, 
                'id_base'   => 'ar_ads_widget' 
            )
        );

	}

	/**
	 * Widget Form
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
        $defaults = array( 
            'title'     => 'Advertisement',
            'adcode'    => '', 
            'image'     => '', 
            'href'      => '', 
            'alt'       => '' 
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        /* Make the ad code read-only if the user can't work with unfiltered HTML. */
        $read_only = '';

        if ( !current_user_can( 'unfiltered_html' ) ) {
            $read_only = ' readonly="readonly"';
        } ?>

		<!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" />
        </p>

        <!-- Widget Ad Code: Textarea -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'adcode' ) ); ?>"><?php esc_html_e( 'Ad Code:', 'creme-plugin' ); ?></label>
            <textarea class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'adcode' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'adcode' ) ); ?>"<?php echo esc_attr( $read_only ); ?>><?php echo wp_kses_post( $instance['adcode'] ); ?></textarea>
        </p>

        <p><strong><?php esc_html_e( 'Or', 'creme-plugin' ); ?></strong></p>

        <!-- Widget Image: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image URL:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="url" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" value="<?php echo esc_url( $instance['image'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" />
        </p>

        <!-- Widget Href: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>"><?php esc_html_e( 'Link URL:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="url" name="<?php echo esc_attr( $this->get_field_name( 'href' ) ); ?>" value="<?php echo esc_url( $instance['href'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>" />
        </p>

        <!-- Widget Alt Text: Text Input -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>"><?php esc_html_e( 'Alt text:', 'creme-plugin' ); ?></label>
            <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'alt' ) ); ?>" value="<?php echo esc_attr( $instance['alt'] ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>" />
        </p>

<?php

	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] 	= strip_tags( $new_instance['title'] );
        $instance['adcode'] = wp_kses_post( $new_instance['adcode'] );
        $instance['image'] 	= esc_url( $new_instance['image'] );
        $instance['href'] 	= esc_url( $new_instance['href'] );
        $instance['alt'] 	= esc_attr( $new_instance['alt'] );

        if ( !current_user_can( 'unfiltered_html' ) ) {
            $instance['adcode'] = $old_instance['adcode'];
        }

        return $instance;

	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {

		extract( $args );

		/* Our variables from the widget settings. */
		$title 		 = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$adcode 	 = isset( $instance['adcode'] ) ? $instance['adcode'] : '';
		$image 		 = isset( $instance['image'] ) ? esc_url( $instance['image'] ) : '';
		$href 		 = isset( $instance['href'] ) ? esc_url( $instance['href'] ) : '';
		$alt  		 = isset( $instance['alt'] ) ? esc_attr( $instance['alt'] ) : '';

		/* Before widget (defined by themes). */
		print( $before_widget );

		/* Display the widget title if one was input (before and after defined by themes). */
        if ( $title )
            print( $before_title . $title . $after_title );

		if ( $adcode != '' ) : 
            echo wp_kses_post( $adcode );
        else :
            if ( $href != '' ) : ?>
                <a href="<?php echo esc_url( $href ); ?>">

                    <?php if ( $image != '' ) : ?>
                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
                    <?php else :
                        if ( $alt != '' ) {
                            echo esc_html( $alt );
                        }
                    endif; ?>

                </a>
            <?php
            endif;

        endif;

		/* After widget (defined by themes). */
		print( $after_widget );

	}

}