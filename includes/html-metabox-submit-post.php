<?php
/**
 * Meta Box - Submit Action.
 *
 * @author Marko Nikolic
 * @package acf_fcl
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly
?>

<div id="submitpost" class="submitbox">
	<div id="major-publishing-actions">
		<div id="publishing-action">
			<span class="spinner"></span>
			<?php submit_button( esc_attr( 'Update' ), 'primary', 'submit', false );?>
		</div>
		<div class="clear"></div>
	</div><!-- #major-publishing-actions -->
</div><!-- #submitpost -->
