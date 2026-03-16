<?php

defined('ABSPATH') || exit;

class itw_converter{
	public function convertImage( $path, $quality ){
		ini_set( 'memory_limit', '1G' );
		set_time_limit( 120 );

		$output = $path . '.webp';

		try{
			$image = new Imagick( $path );
			$image->setImageFormat('WEBP');
			$image->stripImage();
			$image->setImageCompressionQuality( $quality );
			$blob = $image->getImageBlob();
			$success = file_put_contents( $output, $blob );
		}catch( \Throwable $e ){
			error_log( print_r( $e, 1 ) );
			return false;
		}

		return [
			'path' => $output,
			'size' => [
				'before' => filesize( $path ),
				'after' => filesize( $output )
			]
		];
	}
}