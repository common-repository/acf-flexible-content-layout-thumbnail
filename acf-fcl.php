<?php
/**
 * Plugin Name: ACF Flexible Content Layout Thumbnail
 * Plugin URI: http://www.webcentar.net/
 * Description: Extend Advanced Custom Fields PRO - add thumbnail layout to flexible content
 * Version: 1.0.0
 * Author: Marko Nikolic
 * Author URI: http://www.webcentar.net
 * Requires at least: 1.0
 * Tested up to: 1.0
 * Tags: acf, custom, field, fields, flexible, flexible content, flexible layout, thumbnail, thumbnails
 *
 * Text Domain: acf_fcl
 *
 * @package acf_fcl
 * @category Core
 * @author Marko Nikolic
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.


if ( ! class_exists( 'acf_fcl' ) ) :

/**
 * Main acf_fcl Class.
 *
 * @class acf_fcl
 * @version	1.0.0
 */
final class acf_fcl {

	/**
	 * acf_fcl version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * acf_fcl plugin slug.
	 *
	 * @var string
	 */
	public $plugin_slug = 'acf_fcl';

	/**
	 * acf_fcl options name.
	 *
	 * @var string
	 */
	public $option_name = 'acf_fcl_option';

	/**
	 * The Settings Page Screen id.
	 *
	 * @var string
	 */
	public $screen_id = 'toplevel_page_acf_fcl';
	
	/**
	 * No Image Url.
	 *
	 * @var string
	 */
	public $no_image_url = 'assets/images/noImage.png';

	/**
	 * The single instance of the class.
	 *
	 * @var acf_fcl
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Main acf_fcl Instance.
	 *
	 * Ensures only one instance of acf_fcl is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @return acf_fcl - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * acf_fcl Constructor.
	 * @since 1.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( $this, 'admin_activation_notice' ) );
		add_action( 'admin_notices', array( $this, 'admin_hook_notice' ) );
		add_filter('acf/fields/flexible_content/layout_title', array( $this, 'acf_fclt' ), 10, 4);
	}

	/**
	 * Define acf_fcl Constants.
	 */
	private function define_constants() {
		$this->define( 'FCL_ABSURL', plugins_url( '/', __FILE__ ) );
		$this->define( 'FCL_ABSPATH', dirname( __FILE__ ) . '/' );
		$this->define( 'FCL_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin.
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once( FCL_ABSPATH . 'includes/class-meta-boxes.php' );
			include_once( FCL_ABSPATH . 'includes/class-menus.php' );
			include_once( FCL_ABSPATH . 'includes/class-admin-page.php' );
		}
	}

	/**
	 * Add Notice if exist error when plugin activation.
	 */
	public function admin_activation_notice() {
		add_action( 'admin_notices', array( $this, 'admin_hook_notice' ) );
	}

	/**
	 * Add Notice if exist error.
	 */
	public function admin_hook_notice() {
		if ( ! function_exists('acf_get_field_groups') ) {
			global $pagenow;
			if ( $pagenow == 'plugins.php' ) : ?>
				<div class="notice notice-error is-dismissible">
					<p><?php _e( '<strong>Error: </strong>Plugin <strong>ACF Flexible Content Layout Thumbnail </strong>can not work without plugin Advanced Custom Fields PRO.<br/>Advanced Custom Fields PRO plugin is not active, please activate the plugin!', 'error' ); ?></p>
				</div>
			<?php else :
				$message = 'Error: Plugin ACF Flexible Content Layout Thumbnail can not work without plugin Advanced Custom Fields PRO.<br/>Advanced Custom Fields PRO plugin is not active, please activate the plugin!';
				add_settings_error( 'acf_fcl_notice', 'acf_fcl_notice', $message, 'error' );
			endif;
		}
	}

	/**
	 * Check if there is a required function.
	 */
	public function check_exist_acf() {
		if ( ! function_exists('acf_get_field_groups') ) {
			return true;
		}
		return false;
	}

	/**
	 * Adding data to the header flexible content section
	 */
	public function acf_fclt( $title, $field, $layout, $i ) {
		$layoutImages = acf_fcl_meta_boxes::get_data();

		if ( array_key_exists( $layout['key'], $layoutImages ) ) {
			$image = wp_get_attachment_image_src( $layoutImages[$layout['key']], 'full' );
			if ( isset( $image[0] ) && !empty( $image[0] ) ) {
				$title .= '<span style="position: absolute; top: 0; left: auto; margin-left: 15px;">';
				$title .= '<img src="' . $image[0] . '" height="36px" />';
				$title .= '</span>';
			}
		}
		return $title;
	}
}

endif;



/**
 * Main instance of acf_fcl_instance.
 *
 * Returns the main instance of acf_fcl_instance to prevent the need to use globals.
 *
 * @since  1.0
 * @return acf_fcl
 */
function acf_fcl_instance() {
	return acf_fcl::instance();
}

// Global for backwards compatibility.
$GLOBALS['acf_fcl'] = acf_fcl_instance();
