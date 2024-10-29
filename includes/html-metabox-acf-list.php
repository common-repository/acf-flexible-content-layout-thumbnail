<?php
/**
 * Meta Box - Flexible Content Layout List.
 *
 * @author Marko Nikolic
 * @package acf_fcl
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

// 
global $acf_fcl;

// no image url
$noImage = FCL_ABSURL.$acf_fcl->no_image_url;

// get all field groups
$groups = acf_get_field_groups();

// all option data
$layoutImages = acf_fcl_meta_boxes::get_data(); ?>

<style type="text/css">
	ul.acf-wrap {
		display: block;
		position: relative;
		margin: 20px 0 0;
		border: 1px solid #E1E1E1;
		color: #333333;
		font-size: 14px;
		line-height: 1.4em;
	}
	ul.acf-wrap li ul.acf-content {
		margin: 10px 10px;
		border: 1px solid #E1E1E1;
	}
	ul.acf-wrap > li.acf-group-title {
		padding: 8px 10px;
		font-weight: 700;
		border-bottom: 1px solid #E1E1E1;
	}
	ul.acf-wrap li .acf-type {
		font-weight: 400;
		float: right;
	}
	ul.acf-wrap li ul li {
		border-bottom: 1px solid #E1E1E1;
		margin: 8px 0;
		padding: 0px 0px 8px 8px;
		float: none;
		clear: both;
		overflow: hidden;
	}
	ul.acf-wrap li ul li:last-child {
		border-bottom: 0;
		padding-bottom: 0;
	}
	ul.acf-wrap li ul li.acf-field-label {
		font-weight: 700;
	}
	ul.acf-wrap li ul li .acf-image {
		display: inline-block;
		box-sizing: border-box;
		border: 1px solid #E1E1E1;
		font-size: 0px;
		line-height: 0em;
		margin-right: 15px;
		float: left;
	}
	ul.acf-wrap li ul li .acf-image img {
		width: 50px;
		height: 50px;
	}
	ul.acf-wrap li ul li .acf-title {
		display: inline-block;
		margin-top: 15px;
		margin-right: 25px;
		font-weight: 400;
		text-transform: uppercase;
		float: left;
	}
	ul.acf-wrap li ul li .acf-input-file {
		margin-top: 10px;
		float: left;
	}
</style>

<div class="acf-wrap-section">
	<?php $i = 1; foreach ( $groups as $group ) : ?>
	<?php $fields = acf_get_fields( $group['ID'] ); ?>
	<?php if ( !empty( $fields ) && $fields[0]['type'] == 'flexible_content' ) : ?>
	<ul class="acf-wrap">
		<li class="acf-group-title"><?php echo $i; ?>. Field Groups: <?php echo $group['title']; ?>
			<span class="acf-type">Field Type: Flexible Content</span>
		</li>
		<li>
			<?php foreach ( $fields as $key => $field ) : ?>
			<ul class="acf-content">
				<li class="acf-field-label">Field Label: <?php echo $field['label']; ?></li>
				<?php foreach ($field['layouts'] as $layout) : ?>
				<li>
					<span class="acf-image">
						<?php if ( array_key_exists( $layout['key'], $layoutImages ) ) : ?>
							<?php $image = wp_get_attachment_image_src( $layoutImages[$layout['key']], 'full' ); ?>
							<?php if ( isset( $image[0] ) && !empty( $image[0] ) ) : ?>
								<img src="<?php echo $image[0] ?>" />
							<?php else : ?>
								<img src="<?php echo $noImage; ?>" />
							<?php endif; ?>
						<?php else : ?>
							<img src="<?php echo $noImage; ?>" />
						<?php endif; ?>
					</span>
					<span class="acf-title"><?php echo $layout['label']; ?></span>
					<span class="acf-input-file">
						<?php
							$attach_id = '';
							if ( array_key_exists( $layout['key'], $layoutImages ) ) {
								$image = wp_get_attachment_image_src( $layoutImages[$layout['key']], 'full' );
								if ( isset( $image[0] ) && !empty( $image[0] ) ) {
									$attach_id = $layoutImages[$layout['key']];
								}
							}
						?>
						<input class="acf-image-file" type="hidden" name="acf[<?php echo $layout['key']; ?>]" value="<?php echo esc_attr($attach_id); ?>">
						<span class="hide-if-no-js">
							<a class="button upload-custom-img <?php if( $attach_id ) { echo 'hidden'; } ?>" href="#">Add Image</a>
							<a class="button delete-custom-img <?php if( !$attach_id ) { echo 'hidden'; } ?>" href="#">Remove image</a>
						</span>
					</span>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endforeach; ?>
		</li>
	</ul>
	<?php endif; ?>
	<?php $i++; endforeach; ?>
</div>
