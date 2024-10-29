<?php
/**
 * Admin Page Class.
 *
 * @author   Marko Nikolic
 * @package  acf_fcl
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'acf_fcl_admin_page', false ) ) :

/**
 * acf_fcl_admin_page Class.
 */
class acf_fcl_admin_page {

	/**
	 * Admin Page.
	 *
	 * Handles the display of the metabox page in admin.
	 */
	public static function output() {
		
		// global vars
		global $hook_suffix;

		// enable add_meta_boxes function in this page.
		do_action( 'add_meta_boxes', $hook_suffix ); ?>

		<div class="wrap">

			<?php
				// update request
				if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
					$data = acf_fcl_meta_boxes::submit_data();
				}
			?>

			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php settings_errors(); ?>

			<form id="acf_fcl_form" method="post">
				<?php settings_fields( 'acf_fcl' ); // options group  ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php // wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( $hook_suffix, 'side', null ); ?>
							<!-- #side-sortables -->
						</div><!-- #postbox-container-1 -->
						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( $hook_suffix, 'normal', null ); ?>
							<!-- #normal-sortables -->
							<?php do_meta_boxes( $hook_suffix, 'advanced', null ); ?>
							<!-- #advanced-sortables -->
						</div><!-- #postbox-container-2 -->
					</div><!-- #post-body -->
					<br class="clear">
				</div><!-- #poststuff -->
			</form>
		</div><!-- .wrap -->
	<?php
	}
}

endif;
