<?php

defined('ABSPATH') || exit;

if( ! function_exists('itw_render_select') ){
	function itw_render_select( $field_data = [], $print = 1, $cols = [ 'value' => 'ID', 'text' => 'post_title' ] ){
		if( ! is_object( $field_data ) ) $field_data = (object)$field_data;
		$field_data->value = is_array( $field_data->value ) ? $field_data->value : [ $field_data->value ];
		$select = sprintf(
			'<select name="%s" id="%s" %s %s>',
			esc_attr( $field_data->name ),
			esc_attr( $field_data->id ),
			isset( $field_data->multiple ) ? 'multiple' : '',
			isset( $field_data->size ) ? 'size="' . intval( $field_data->size ) . '"' : ''
		);
		if( isset( $field_data->placeholder ) ){
			$select .= '<option value="" disabled>' . esc_html( $field_data->placeholder ) . '</option>';
		}
		foreach( $field_data->options as $option => $value ){
			if( isset( $value->ID ) || isset( $value->term_id ) ){
				$post_id = isset( $value->ID ) ? $value->ID : $value->term_id;
				$value = (array)$value;
				if( class_exists( 'PLL_Model' ) ){
					$post_lang = pll_get_post_language( $post_id );
					if( pll_default_language() != $post_lang ) continue;
				}
				$select .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value[ $cols['text'] ] ),
					in_array( $value[ $cols['text'] ] , $field_data->value ) ? 'selected' : '',
					esc_html( $value[ $cols['value'] ] )
				);
			}else{
				$select .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $option ),
					in_array( $option, $field_data->value ) ? 'selected' : '',
					esc_html( $value )
				);
			}
		}
		$select .= '</select>';
		$select = wp_kses(
			$select,
			[
				'select' => [
					'id' => [],
					'name' => [],
					'multiple' => [],
					'size' => [],
				],
				'option' => [
					'value' => [],
					'selected' => [],
					'disabled' => [],
				],
			]
		);
		if( $print )
			echo $select;
		else
			return $select;
	}
}

if( isset( $_POST['plugin_sent'] ) && check_admin_referer('itw_general') ) echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'images-to-webp' ) . '</p></div>'; ?>

<form method="post" action="">
	<?php wp_nonce_field('itw_general') ?>
	<input type="hidden" name="plugin_sent" value="1">
	<table class="form-table">
		<tr>
			<th>
				<label for="q_field_1"><?php esc_html_e( 'Images quality', 'images-to-webp' ) ?></label> 
			</th>
			<td>
				<input type="number" min="1" max="100" step="1" name="webp_quality" placeholder="85" value="<?php echo intval( $this->settings['webp_quality'] ) ?>" id="q_field_1"> %
			</td>
		</tr>
		<tr>
			<th>
				<label for="q_field_2"><?php esc_html_e( 'Convert images to WebP during upload', 'images-to-webp' ) ?></label> 
			</th>
			<td><?php
				itw_render_select([
					'name' => 'upload_convert',
					'id' => 'q_field_2',
					'value' => $this->settings['upload_convert'],
					'options' => [
						0 => __( 'No', 'images-to-webp' ),
						1 => __( 'Yes', 'images-to-webp' )
					]
				]); ?>
			</td>
		</tr>
		<tr>
			<th>
				<label for="q_field_3"><?php esc_html_e( 'Conversion method', 'images-to-webp' ) ?></label> 
			</th>
			<td><?php
				itw_render_select([
					'name' => 'method',
					'id' => 'q_field_3',
					'value' => $this->settings['method'],
					'options' => get_site_option('images_to_webp_methods')
				]); ?>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php esc_html_e( 'Convert these image extensions to WebP:', 'images-to-webp' ) ?></label> 
			</th>
			<td>
				<?php foreach( $this->extensions as $images_to_webp_extension ): ?>
					<br>
					<label>
						<input type="checkbox" name="extensions[]" value="<?php echo esc_attr( $images_to_webp_extension ) ?>" <?php echo in_array( $images_to_webp_extension, $this->settings['extensions'] ) ? 'checked="checked"' : '' ?>>
						.<?php echo esc_attr( $images_to_webp_extension ) ?>
					</label>
				<?php endforeach ?>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php esc_html_e( 'Delete original images after conversion', 'images-to-webp' ) ?></label> 
			</th>
			<td><?php
				itw_render_select([
					'name' => 'delete_originals',
					'id' => 'q_field_4',
					'value' => isset( $this->settings['delete_originals'] ) ? $this->settings['delete_originals'] : 0,
					'options' => [
						0 => __( 'No', 'images-to-webp' ),
						1 => __( 'Yes', 'images-to-webp' )
					]
				]); ?>
				<section class="notice notice-alt notice-error">
					<p><strong><?php esc_html_e( 'Be EXTREMELY CAREFUL with this option!', 'images-to-webp' ) ?></strong></p>
					<p>
						<?php esc_html_e( 'This will PERMANENTLY DELETE ORIGINAL IMAGES (only WebP versions will exist)!', 'images-to-webp' ) ?><br>
						<?php esc_html_e( 'It is a good idea to create some backup.', 'images-to-webp' ) ?><br>
						<?php esc_html_e( 'You CAN NOT DEACTIVATE THIS PLUGIN when you use this option, otherwise all converted images will throw 404 ERROR!', 'images-to-webp' ) ?><br>
						<small><?php esc_html_e( '(Of course, you can activate it back to fix this problem.)', 'images-to-webp' ) ?></small>
					</p>
				</section>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php esc_html_e( 'Save', 'images-to-webp' ) ?>"></p>
</form>