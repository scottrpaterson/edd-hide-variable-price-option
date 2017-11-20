<?php
/*
Plugin Name: EDD Hide Variable Price Option
Description: Allow you to hide a variable price option
Version: 1.0
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/





/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Add custom meta box for 'download' */
add_action( 'add_meta_boxes', 'edd_hvpo_create_meta_boxes' );

/* Save metabox data. */
add_action( 'save_post', 'edd_hvpo_save_meta_boxes', 10, 2 );


/**
 * Add Download Info Meta Box.
 *
 * @since 0.1
 */
function edd_hvpo_create_meta_boxes() {

	add_meta_box( 'edd_hvpo_metabox', esc_html__( 'Hide Options', 'edd-download-info' ), 'edd_hvpo_class_meta_box', 'download', 'side', 'core' );

}

/**
 * Display the download info meta box.
 *
 * @since 0.1
 */
function edd_hvpo_class_meta_box( $object, $box ) {
	global $post;
	?>

	<?php wp_nonce_field( basename( __FILE__ ), 'edd_hvpo_meta_box_nonce' ); ?>

	<p>
		<label for="download_demo_linka"><?php _e( "Price Option ID to Hide<br />(seperate by a comma)", 'edd-download-info' ); ?></label>
		<br />
		<input class="widefat" type="text" name="price_options_to_hide" value="<?php echo get_post_meta($post->ID,'price_options_to_hide',true); ?>" size="30" />
	</p>
	
	<?php
}

/**
 * Save data from download info meta box.
 *
 * @since 0.1
 */
function edd_hvpo_save_meta_boxes( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['edd_hvpo_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['edd_hvpo_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;
		
	/* Check autosave. */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$price_options_to_hide = sanitize_text_field($_POST['price_options_to_hide']);

	update_post_meta( $post_id,'price_options_to_hide',$price_options_to_hide);
}






/**
 * Remove price option from array
 *
 * @since 1.0
 */
function edd_hvpo_price_filter($variable_prices,$download_id) {

	//$to_remove = array('1');
	$to_remove = get_post_meta($download_id,'price_options_to_hide',true);
	$to_remove = explode(",", $to_remove);
	
	
	

	foreach ($to_remove as $remove) {
		
		unset($variable_prices[$remove]);
		
	}

	//echo "<pre>";
	//print_r($post_id);

	return $variable_prices;

}
add_filter('edd_purchase_variable_prices','edd_hvpo_price_filter', 10, 2 );
