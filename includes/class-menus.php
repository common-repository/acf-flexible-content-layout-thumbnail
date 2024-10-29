<?php
/**
 * Setup menus in WP admin.
 *
 * @author   Marko Nikolic
 * @package  acf_fcl
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'acf_fcl_menu', false ) ) :

/**
 * acf_fcl_menu Class.
 */
class acf_fcl_menu {

	/**
	 * Hook in admin menu.
	 */
	public function __construct() {
		// Add menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		// Register our setting.
		register_setting( 'acf_fcl', 'acf_fcl_basic', 'admin_basic_sanitize' );

		// Add settings menu page
		add_menu_page(
			__( 'ACF Flexible Content Layout Thumbnail', 'acf_fcl' ),
			__( 'ACF Flexible Thumbnail', 'acf_fcl' ),
			'manage_options',
			'acf_fcl',
			array( $this, 'admin_menu_page' ),
			'dashicons-align-left'
			// '95'
		);
	}

	/**
	 * Sanitize Basic Settings
	 * This function is defined in register_setting().
	 */
	public function admin_basic_sanitize( $settings ) {
		$settings = sanitize_text_field( $settings );
		return $settings;
	}

	/**
	 * Init the admin page.
	 */
	public function admin_menu_page() {
		acf_fcl_admin_page::output();
	}
}

endif;

return new acf_fcl_menu();
