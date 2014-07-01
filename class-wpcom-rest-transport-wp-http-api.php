<?php

class WPCOM_REST_Transport_WP_HTTP_API extends WPCOM_REST_Transport {

	public function __construct() {
		if ( ! class_exists( 'WP_Http' ) ) {
			throw new BadMethodCallException( 'This transport requires the WordPress HTTP API.' );
		}
	}

	public function send_request( $url, $method, $post_data = array(), $headers = array() ) {
		$args = array(
			'body' => $post_data,
			'headers' => $headers,
		);

		if ( WPCOM_REST_Client::REQUEST_METHOD_GET === $method ) {
			$response = wp_remote_get( $url, $args );
		} elseif ( WPCOM_REST_Client::REQUEST_METHOD_POST === $method ) {
			$response = wp_remote_post( $url, $args );
		}

		$response_code = wp_remote_retrieve_response_code( $response ); 
		$response_body = wp_remote_retrieve_body( $response );
		if ( is_wp_error( $response ) ) {
			return $this->handle_error( $response->get_error_message(), $response->get_error_code() );
		} elseif ( ! $this->is_valid_response_code( $response_code ) ) {
			return $this->handle_error( $response_body, $response_code ); 
		}

		$body = $response_body;
		return $this->handle_success( $body );
	}
}	


