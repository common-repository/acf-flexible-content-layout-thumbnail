<?php
/**
 * Meta Boxes Class.
 *
 * @author Marko Nikolic
 * @package acf_fcl
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'acf_fcl_meta_boxes', false ) ) :

/**
 * acf_fcl_meta_boxes Class.
 */
class acf_fcl_meta_boxes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		// Load the JavaScript needed for the settings screen.
		add_action( 'admin_enqueue_scripts', array( $this, 'acf_fcl_enqueue_scripts' ) );
		add_action( 'init',  array( $this, 'admin_footer_screen_id' ) );
		add_filter( 'screen_layout_columns', 'acf_fcl_screen_layout_column', 10, 2 );
	}

	/**
	 * Screen ID
	 */
	public function screen_id() {
		global $acf_fcl;
		return $acf_fcl->screen_id;
	}

	/**
	 * No Image Url
	 */
	public function no_image_url() {
		global $acf_fcl;
		return FCL_ABSURL.$acf_fcl->no_image_url;
	}

	/**
	 * Admin Footer Enqueue Scripts
	 */
	public function admin_footer_screen_id() {
		add_action( 'admin_footer-'.$this->screen_id(), array( $this, 'acf_fcl_footer_scripts' ) );
	}

	/**
	 * Add Meta boxes.
	 */
	public function add_meta_boxes() {
		global $acf_fcl;
		if ( $this->screen_id() == 'toplevel_page_acf_fcl' && !$acf_fcl::check_exist_acf() ) {
			add_meta_box( 'submitdiv', 'Save Options', 'acf_fcl_meta_boxes::meta_box_submit_post', $this->screen_id(), 'side', 'high' );
			add_meta_box( 'acffcsdiv', 'ACF Section Thumbnails', 'acf_fcl_meta_boxes::meta_box_acf_fcl', $this->screen_id(), 'normal', 'high' );
		}
	}

	/**
	 * Add Meta Box Submit Action.
	 */
	public function meta_box_submit_post() {
		include( FCL_ABSPATH . 'includes/html-metabox-submit-post.php' );
	}

	/**
	 * Add Meta Box Flexible Content List.
	 */
	public function meta_box_acf_fcl() {
		include( FCL_ABSPATH . 'includes/html-metabox-acf-list.php' );
	}

	/**
	 * Get Option Data.
	 */
	public function get_data() {
		global $acf_fcl;
		$option_name = $acf_fcl->option_name;

		$option_value = get_option( $option_name );
		if ( empty( $option_value ) ) {
			$option_value = json_encode( array() );
		}
		return json_decode( $option_value, true );
	}

	/**
	 * Submit Action.
	 */
	public function submit_data() {
		global $acf_fcl;
		$option_name = $acf_fcl->option_name;

		if ( isset( $_POST['acf'] ) && !empty( $_POST['acf'] ) ) {
			$data_option = array_filter( $_POST['acf'] );
			$data_option = json_encode( $data_option );

			$action = update_option( $option_name, $data_option );
			if ( $action ) {
				$message = 'Settings updated.';
				add_settings_error( 'acf_fcl_notice', 'acf_fcl_notice', $message, 'updated' );
			}
			else {
				$message = '<strong>ERROR:</strong> Settings not updated.';
				add_settings_error( 'acf_fcl_notice', 'acf_fcl_notice', $message, 'error' );
			}
		}
		else {
			$message = '<strong>ERROR:</strong> Settings not updated.';
			add_settings_error( 'acf_fcl_notice', 'acf_fcl_notice', $message, 'error' );
		}
	}

	/**
	 * Number of Column available in Settings Page.
	 * we can only set to 1 or 2 column.
	 */
	function acf_fcl_screen_layout_column( $columns, $screen ) {
		$screen_id = $this->screen_id();
		if ( $screen == $screen_id ) {
			$columns[$screen_id] = 2;
		}
		return $columns;
	}

	/**
	 * Load Script Needed For Meta Box
	 */
	public function acf_fcl_enqueue_scripts( $hook_suffix ) {
		$screen_id = $this->screen_id();
		if ( $hook_suffix == $screen_id ) {
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
			wp_enqueue_media();
		}
	}

	/**
	 * Footer Script Needed for Meta Box:
	 * - Meta Box Toggle
	 * - Spinner for Saving Option
	 * - Reset Settings Confirmation
	 * - Add WP Media Frame
	 */
	public function acf_fcl_footer_scripts() { ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function( $ ) {

			var frame;
			metaBox = $('.acf-wrap-section');
			addImgLink = '';
			delImgLink = '';
			imgContainer = '';
			imgIdInput = '';

			metaBox.on('click', '.upload-custom-img', function( e ) {
				e.preventDefault();

				parentBox = $(this).parent().parent().parent();
				addImgLink = parentBox.find('.upload-custom-img');
				delImgLink = parentBox.find('.delete-custom-img');
				imgContainer = parentBox.find('.acf-image');
				imgIdInput = parentBox.find('.acf-image-file');

				if (frame) { frame.open(); return; }
			
				frame = wp.media({
					title: 'Select Image',
					library: { type: 'image' },
					// button: { text: 'Use this media' },
					multiple: false
				});

				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					imgContainer.html('<img src="'+attachment.url+'" />');
					imgIdInput.val(attachment.id);
					addImgLink.addClass( 'hidden' );
					delImgLink.removeClass( 'hidden' );
				});

				frame.open();
			});

			metaBox.on( 'click', '.delete-custom-img', function( e ) {
				e.preventDefault();
				parentBox = $(this).parent().parent().parent();
				addImgLink = parentBox.find('.upload-custom-img');
				delImgLink = parentBox.find('.delete-custom-img');
				imgContainer = parentBox.find('.acf-image');
				imgIdInput = parentBox.find('.acf-image-file');

				imgContainer.html('<img src="<?php echo $this->no_image_url(); ?>" />');
				addImgLink.removeClass('hidden');
				delImgLink.addClass('hidden');
				imgIdInput.val('');
			});

		});

		jQuery(document).ready( function( $ ) {
			// toggle
			/*$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( "<?php $this->screen_id(); ?>" );*/

			// display spinner
			$('#acf_fcl_form').submit( function(){
				$('#publishing-action .spinner').css('visibility', 'visible');
			});
		});
		//]]>
	</script>
	<?php
	}
}

endif;

new acf_fcl_meta_boxes();
