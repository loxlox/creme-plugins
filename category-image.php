<?php

if ( !defined( 'ABSPATH' ) ) { exit; }

/**
 * Categories Images Plugin allow you to add an image to category or any custom term.
 *
 * @package    ARCore
 * @subpackage Functions
 * @author     ARTheme
 * @copyright  Copyright (c) 2015
 */

function category_init() {

	$taxonomies = get_taxonomies();

	if ( is_array( $taxonomies ) ) {

		foreach ( $taxonomies as $taxonomy ) {

	        add_action( $taxonomy . '_add_form_fields', 'add_texonomy_field' );
			add_action( $taxonomy . '_edit_form_fields', 'edit_texonomy_field' );
			add_filter( 'manage_edit-' . $taxonomy . '_columns', 'taxonomy_columns' );
			add_filter( 'manage_' . $taxonomy . '_custom_column', 'taxonomy_column', 10, 3 );

	    }

	}

}

add_action( 'admin_init', 'category_init' );

// add image field in add form
function add_texonomy_field() {

	if ( get_bloginfo( 'version' ) >= 3.5 ) {
		wp_enqueue_media();
	} else {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
	} ?>

	<div class="form-field ar-field">
		<label for="taxonomy_image"><?php esc_html_e( 'Image', 'creme-di-menta' ) ?></label>
		<input type="text" name="taxonomy_image" id="taxonomy_image" value="" />
		<br/>
		<button class="upload_image_button button"><?php esc_html_e( 'Upload image', 'creme-di-menta' ) ?></button>
	</div>

<?php

}

// add image field in edit form
function edit_texonomy_field( $taxonomy ) {

	if ( get_bloginfo( 'version' ) >= 3.5 ) {
		wp_enqueue_media();
	} else {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
	}

	if ( taxonomy_image_url( $taxonomy->term_id, NULL, TRUE ) == DEFAULT_IMAGE ) :
		$image_text = "";
	else :
		$image_text = taxonomy_image_url( $taxonomy->term_id, NULL, TRUE ); ?>

		<tr class="form-field ar-field">
			<th scope="row" valign="top"><label for="taxonomy_image"><?php esc_html_e( 'Image', 'creme-di-menta' ) ?></label></th>
			<td>
				<img id="tax-img" class="taxonomy-image" src="<?php echo esc_url( $image_text ) ?>"/>
				<br />
				<input type="text" name="taxonomy_image" id="taxonomy_image" value="<?php echo esc_attr( $image_text ) ?>" />
				<br />
				<button class="upload_image_button button"><?php esc_html_e( 'Upload image', 'creme-di-menta' ) ?></button>
				<button class="remove_image_button button"><?php esc_html_e( 'Remove image', 'creme-di-menta' ) ?></button>
			</td>
		</tr>

<?php endif;

}

function quick_edit_custom_box( $column_name, $screen, $name ) {

	if ( $column_name == 'thumb' ) : ?>

		<fieldset>
			<div class="thumb inline-edit-col ar-quick-edit">
				<label>
					<span class="title"><img src="" alt="Thumbnail"/></span>
					<span class="input-text-wrap"><input type="text" name="taxonomy_image" value="" class="tax_list" /></span>
					<span class="input-text-wrap">
						<button class="upload_image_button button"><?php esc_html_e( 'Upload image', 'creme-di-menta' ) ?></button>
						<button class="remove_image_button button"><?php esc_html_e( 'Remove image', 'creme-di-menta' ) ?></button>
					</span>
				</label>
			</div>
		</fieldset>

	<?php endif;

}


// save our taxonomy image while edit or save term
add_action( 'edit_term', 'save_taxonomy_image' );
add_action( 'create_term', 'save_taxonomy_image' );
function save_taxonomy_image( $term_id ) {

	if( isset( $_POST[ 'taxonomy_image' ] ) ) {
		update_option( 'taxonomy_image_' . $term_id, $_POST[ 'taxonomy_image' ] );
	}

}

// get attachment ID by image url
function get_attachment_id_by_url( $image_src ) {

    global $wpdb;

    $id = $wpdb->get_var( 
    	$wpdb->prepare( 
    		"SELECT ID FROM $wpdb->posts WHERE guid = %s", $image_src 
    	) 
    );

    return ( !empty( $id ) ) ? $id : NULL;

}


/**
 * Thumbnail column added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @return void
 */

function taxonomy_columns( $columns ) {

	$new_columns 			= array();
	$new_columns['cb'] 		= $columns['cb'];
	$new_columns['thumb'] 	= esc_html__( 'Image', 'creme-di-menta' );

	unset( $columns['cb'] );

	return array_merge( $new_columns, $columns );

}

/**
 * Thumbnail column value added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @param mixed $column
 * @param mixed $id
 * @return void
 */
function taxonomy_column( $columns, $column, $id ) {

	if ( $column == 'thumb' ) {
		$columns = '<span><img src="' . taxonomy_image_url( $id, NULL, TRUE ) . '" alt="' . esc_html__( 'Thumbnail', 'creme-di-menta' ) . '" class="wp-post-image" /></span>';
	}

	return $columns;

}

// change 'insert into post' to 'use this image'
function change_insert_button_text( $safe_text, $text ) {
    return str_replace( 'Insert into Post', 'Use this image', $text );
}

// style the image in category list
if ( strpos( $_SERVER['SCRIPT_NAME'], 'edit-tags.php' ) > 0 ) {
	add_action( 'quick_edit_custom_box', 'quick_edit_custom_box', 10, 3 );
	add_filter( 'attribute_escape', 'change_insert_button_text', 10, 2 );
}

// get taxonomy image url for the given term_id (Place holder image by default)
function taxonomy_image_url( $term_id = NULL, $size = NULL, $return_placeholder = FALSE ) {

	if ( !$term_id ) {

		if ( is_category() ) {
			$term_id = get_query_var( 'cat' );
		} elseif ( is_tax() ) {
			$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$term_id = $current_term->term_id;
		}

	}

    $taxonomy_image_url = get_option( 'taxonomy_image_' . $term_id );

    if( !empty( $taxonomy_image_url ) ) {

	    $attachment_id = get_attachment_id_by_url( $taxonomy_image_url );

	    if( !empty( $attachment_id ) ) {

	    	if ( empty( $size ) ) {
	    		$size = 'full';
	    	}

	    	$taxonomy_image_url = wp_get_attachment_image_src( $attachment_id, $size );

		    $taxonomy_image_url = $taxonomy_image_url[0];

	    }

	}

    if ( $return_placeholder ) {
		return ( $taxonomy_image_url != '' ) ? $taxonomy_image_url : DEFAULT_IMAGE;
    } else {
		return $taxonomy_image_url;
    }

}

// get taxonomy image for the given term_id
function taxonomy_image( $term_id = NULL, $size = 'full' ) {

	if ( !$term_id ) {

		if ( is_category() ) {
			$term_id = get_query_var( 'cat' );
		} elseif ( is_tax() ) {
			$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$term_id = $current_term->term_id;
		} elseif ( is_tag() ) {
			$term_id = get_query_var( 'tag_id' );
		}

	}

	$taxonomy_image_url = get_option( 'taxonomy_image_' . $term_id );

	if( !empty( $taxonomy_image_url ) ) {

		$attachment_id = get_attachment_id_by_url( $taxonomy_image_url );

		if ( !empty( $attachment_id ) ) {
			$taxonomy_image = wp_get_attachment_image( $attachment_id, $size, FALSE );
		}

	} else {

		$attachment_id 	= get_attachment_id_by_url( $taxonomy_image_url );
		$attachment 	= get_post( $attachment_id );
		$title 			= $attachment->post_title;
		$taxonomy_image = '<img src="' . esc_url( DEFAULT_IMAGE ) . '" class="attachment-full size-full" alt="' . trim( strip_tags( $title ) ) . '" />';

	}

	return $taxonomy_image;

}