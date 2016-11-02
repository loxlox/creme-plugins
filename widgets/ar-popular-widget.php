<?php

/**
 * Plugin Name: Popular Posts Widget
 */

add_action( 'widgets_init', 'ar_widget_poppost_register' );

function ar_widget_poppost_register() {
	register_widget( 'ar_popular_widget' );
}

class ar_popular_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */

	function ar_popular_widget() {

		/* Create the widget. */
		WP_Widget::__construct(
			'ar_popular_widget', 
			esc_html__( 'Creme: Popular Posts', 'creme-plugin' ), 
			array( 
				'classname'		=> 'ar_popular_widget', 
				'description'	=> esc_html__( 'A widget that displays your popular posts by comment count.', 'creme-plugin' ) 
			), 
			array( 
				'width'		=> 250, 
				'height'	=> 350, 
				'id_base'	=> 'ar_popular_widget' 
			)
		);

	}

	/**
	 * Widget Form
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'title'		  => 'Popular Posts', 
			'popular_by'  => 'comment_count',
			'number_post' => '1' 
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$options  = array(
			'comment_count'		=> 'Comments',
			'post_views_count'	=> 'Views',
			'creme_like_post'	=> 'Favorites'
		); ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'creme-plugin' ); ?></label><br />
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'popular_by' ) ); ?>"><?php esc_html_e( 'Popular By', 'creme-plugin' ); ?></label><br />
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'popular_by' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'popular_by' ) ); ?>">
				<?php foreach ( $options as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php $instance['popular_by'] == $key ? selected( $instance['popular_by'], $key ) : ''; ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_post' ) ); ?>"><?php esc_html_e( 'Number of Posts', 'creme-plugin' ); ?></label><br />
			<input class="widefat" type="number" min="1" max="5" id="<?php echo esc_attr( $this->get_field_id( 'number_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_post' ) ); ?>" value="<?php echo esc_attr( $instance['number_post'] ); ?>"/>
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
		$instance['popular_by']	 = esc_attr( $new_instance['popular_by'] );
		$instance['number_post'] = esc_attr( $new_instance['number_post'] );

		return $instance;

	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title  	= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$popular_by = isset( $instance['popular_by'] ) ? esc_attr( $instance['popular_by'] ) : 'comment_count';
		$number 	= isset( $instance['number_post'] ) ? esc_attr( $instance['number_post'] ) : '1';

		$popular = array(
			'nopaging'				=> 0, 
			'post_status'			=> 'publish', 
			'ignore_sticky_posts'	=> 1, 
			'showposts'				=> $number
		);

		if ( $popular_by == 'comment_count' ) {
			$popular[ 'orderby' ] = 'comment_count';
		} elseif ( $popular_by == 'post_views_count' ) {
			$popular[ 'orderby' ]	= 'meta_value_num';
			$popular[ 'meta_key' ]	= 'post_views_count';
		} elseif ( $popular_by == 'creme_like_post' ) {
			$popular[ 'orderby' ]	= 'meta_value_num';
			$popular[ 'meta_key' ]	= 'cremedimenta_like_post';
		}

		$loop = new WP_Query( $popular );

		if ( $loop->have_posts() ) :

			/* Before widget (defined by themes). */
			print( $before_widget );

			if ( $title )
				print( $before_title . $title . $after_title );

			while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<div class="ar-widget-poppost border-bottom">

					<?php get_the_image( array(
						'size' 	 	   => 'rel-post',
						'link_to_post' => true,
						'before' 	   => '<div class="featured-media alignleft">',
						'after'  	   => '</div>'
					) ); ?>

					<div class="ar-pop-content">
						<?php 
						the_title( '<h4 ' . cremedimenta_get_attr( 'entry-title' ) . '><a href="' . esc_url( get_the_permalink() ) . '" rel="bookmark" itemprop="url">', '</a></h4>' );
						cremedimenta_posted_time();
						?>
						<span class="ar-comment">
							<a href="<?php echo esc_url( get_comments_link() ); ?>" title="<?php echo esc_attr__( 'Comment', 'creme-plugin' ) ?>">
			            		<?php comments_number( '0<i class="zmdi zmdi-comment-text"></i>', '1<i class="zmdi zmdi-comment-text"></i>', '%<i class="zmdi zmdi-comment-text"></i>' ); ?>
			            	</a>
			            </span>
			            <?php 
			            echo cremedimenta_get_post_view_count();
			            cremedimenta_like();
			            ?>
					</div>

					<div class="clear"></div>

				</div>

			<?php endwhile;

			/* After widget (defined by themes). */
			print( $after_widget );

		endif;

		wp_reset_postdata();

	}

}
