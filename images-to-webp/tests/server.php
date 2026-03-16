<?php

defined('ABSPATH') || exit;

if( version_compare( PHP_VERSION, '7.0', '<' ) ){
	// to reduce support threads, as the user needs to solve this with his server provider and not with me
	deactivate_plugins( __DIR__ );
	wp_die( esc_html__( 'Please update your PHP to version 7.0 or higher, then try activate Images to WebP again.', 'images-to-webp' ) );
}

if( ! extension_loaded('gd') && ! extension_loaded('imagick') ){
	// to reduce support threads, as the user needs to solve this with his server provider and not with me
	deactivate_plugins( __DIR__ );
	wp_die( esc_html__( 'Please install GD or Imagick on your server, then try activate Images to WebP again.', 'images-to-webp' ) );
}

$itw_methods = [];

if(
	function_exists('imagecreatefromjpeg') &&
	function_exists('imagecreatefrompng') &&
	function_exists('imagecreatefromgif') &&
	function_exists('imageistruecolor') &&
	function_exists('imagepalettetotruecolor') &&
	function_exists('imagewebp')
){
	$itw_methods['gd'] = __( 'GD', 'images-to-webp' );
}

if( extension_loaded('imagick') ){
	if( class_exists('Imagick') ){
		if( in_array( 'WEBP', ( new Imagick() )->queryFormats() ) ){
			$itw_methods['imagick'] = esc_html__( 'Imagick', 'images-to-webp' );
		}
	}
}

if( count( $itw_methods ) === 0 ){
	// to reduce support threads, as the user needs to solve this with his server provider and not with me
	deactivate_plugins( __DIR__ );
	wp_die( esc_html__( 'Please enable WebP in GD or Imagick on your server, then try activate Images to WebP again.', 'images-to-webp' ) );
}

update_site_option( 'images_to_webp_methods', $itw_methods );