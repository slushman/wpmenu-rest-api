<?php

/**
 * Sets CORS headers to allow for reading menus.
 *
 * @link 		https://www.slushman.com
 * @since 		1.0.0
 * @package 	WpmenuRestApi\Includes
 * @author 		Slushman <chris@slushman.com>
 */

namespace WpmenuRestApi\Includes;

class Cors {

	/**
	 * Registers all the WordPress hooks and filters related to this class.
	 *
	 * @hooked 		init
	 * @since 		1.0.0
	 */
	public function hooks() {

		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

		add_filter( 'rest_pre_serve_request', array( $this, 'set_cors' ), 10, 1 );

	} // hooks()

	/**
	 * 
	 */
	public function set_cors( $value ) {

		$origin = get_http_origin();
		
		if ( $origin ) {
		
			header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
		
		}
		
		header( 'Access-Control-Allow-Origin: ' . esc_url_raw( site_url() ) );
		header( 'Access-Control-Allow-Methods: GET' );

		return $value;

	} // set_cors()

} // class