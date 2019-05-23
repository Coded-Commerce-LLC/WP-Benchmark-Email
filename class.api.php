<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// ReST API Class
class wpbme_api {

	// Endpoint
	static $url = 'https://clientapi.benchmarkemail.com/';

	// Adds a Contact To a List
	static function add_contact( $listID, $email, $args = [] ) {
		extract( $args );

		// Build Body
		$body = [
			'Data' => [
				'Email' => $email,
				'EmailPerm' => 1,
				'Field19' => current_time( 'm/d/Y' ),
				'IPAddress' => wpbme_api::get_client_ip(),
			],
		];

		// Order Details
		if( isset( $company ) ) { $body['Data']['Field9'] = $company; }
		if( isset( $first ) ) { $body['Data']['FirstName'] = $first; }
		if( isset( $last ) ) { $body['Data']['LastName'] = $last; }
		if( isset( $phone ) ) { $body['Data']['Field6'] = $phone; }
		if( isset( $product1 ) ) { $body['Data']['Field21'] = $product1; }
		if( isset( $product2 ) ) { $body['Data']['Field22'] = $product2; }
		if( isset( $product3 ) ) { $body['Data']['Field23'] = $product3; }
		if( isset( $total ) ) { $body['Data']['Field24'] = $total; }
		if( isset( $url ) ) { $body['Data']['Field18'] = $url; }

		// Order History
		if( isset( $first_order_date ) ) { $body['Data']['Field25'] = $first_order_date; }
		if( isset( $total_spent ) ) { $body['Data']['Field26'] = $total_spent; }
		if( isset( $total_orders ) ) { $body['Data']['Field27'] = $total_orders; }

		// Billing Address
		if( isset( $b_address ) ) { $body['Data']['Field1'] = $b_address; }
		if( isset( $b_city ) ) { $body['Data']['Field2'] = $b_city; }
		if( isset( $b_state ) ) { $body['Data']['Field3'] = $b_state; }
		if( isset( $b_zip ) ) { $body['Data']['Field4'] = $b_zip; }
		if( isset( $b_country ) ) { $body['Data']['Field5'] = $b_country; }

		// Shipping Address
		if( isset( $s_address ) ) { $body['Data']['Field13'] = $s_address; }
		if( isset( $s_city ) ) { $body['Data']['Field14'] = $s_city; }
		if( isset( $s_state ) ) { $body['Data']['Field15'] = $s_state; }
		if( isset( $s_zip ) ) { $body['Data']['Field16'] = $s_zip; }
		if( isset( $s_country ) ) { $body['Data']['Field17'] = $s_country; }

		// Search Existing Contacts
		$matches = wpbme_api::find_contact( $email );
		foreach( $matches as $match ) {

			// Found Match, Update Record
			if( $match->ContactMasterID == $listID ) {
				$uri = 'Contact/' . $listID . '/ContactDetails/' . $match->ID;
				$response = wpbme_api::benchmark_query( $uri, 'PATCH', $body );
				return isset( $response->ID ) ? intval( $response->ID ) : $response;
			}
		}

		// Add New Contact
		$uri = 'Contact/' . $listID . '/ContactDetails';
		$response = wpbme_api::benchmark_query( $uri, 'POST', $body );

		// Response
		return isset( $response->ID ) ? intval( $response->ID ) : $response;
	}

	// Find Contact ID On a List
	static function find_contact( $email ) {
		$email = str_replace( '+', '%2B', $email );
		return wpbme_api::benchmark_query( 'Contact/ContactDetails?Search=' . $email );
	}

	// Deletes a Contact
	static function delete_contact( $listID, $contactID ) {
		$body = [ 'ContactID' => $contactID, 'ListID' => $listID ];
		return wpbme_api::benchmark_query( 'Contact/ContactDetails', 'DELETE', $body );
	}

	// Find a Contact By Email, Then Delete
	static function delete_contact_by_email( $list_slug, $listID, $email ) {
		$results = self::find_contact( $email );
		if( ! is_array( $results ) ) { return; }
		foreach( $results as $row ) {
			if( $row->ListName == wpbme_frontend::$list_names[$list_slug] ) {
				wpbme_api::delete_contact( $listID, $row->ID );
			}
		}
	}

	// Adds a Contact List
	static function add_list( $name ) {
		$body = [ 'Data' => [ 'Description' => $name, 'Name' => $name ] ];
		$response = wpbme_api::benchmark_query( 'Contact/', 'POST', $body );
		return empty( $response->ID ) ? $response : intval( $response->ID );
	}

	// Get Contact From a List
	static function get_contact( $listID, $contactID ) {
		return wpbme_api::benchmark_query( 'Contact/' . $listID . '/ContactDetails/' . $contactID );
	}

	// Gets Client IP Address
	static function get_client_ip() {
		if( isset( $_SERVER[ 'HTTP_CLIENT_IP' ] ) )
			return $_SERVER[ 'HTTP_CLIENT_IP' ];
		if( isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) )
			return $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		if( isset( $_SERVER[ 'HTTP_X_FORWARDED' ] ) )
			return $_SERVER[ 'HTTP_X_FORWARDED' ];
		if( isset( $_SERVER[ 'HTTP_FORWARDED_FOR' ] ) )
			return $_SERVER[ 'HTTP_FORWARDED_FOR' ];
		if( isset( $_SERVER[ 'HTTP_FORWARDED' ] ) )
			return $_SERVER[ 'HTTP_FORWARDED' ];
		if( isset( $_SERVER[ 'REMOTE_ADDR' ] ) )
			return $_SERVER[ 'REMOTE_ADDR' ];
	}

	// Get All Contact Lists
	static function get_lists() {
		return wpbme_api::benchmark_query( 'Contact/' );
	}

	// Get All Signup Forms
	static function get_forms() {
		return wpbme_api::benchmark_query( 'SignupForm/' );
	}

	// Vendor Handshake
	static function update_partner() {
		$uri = 'Client/Partner';
		$body = [ 'PartnerLogin' => 'beautomated' ];
		return wpbme_api::benchmark_query( $uri, 'POST', $body );
	}

	// Talk To Benchmark ReST API
	static function benchmark_query( $uri = '', $method = 'GET', $body = null, $key = null ) {

		// Organize Request
		if( $body ) { $body = json_encode( $body ); }
		$key = $key ? $key : get_option( 'wpbme_key' );
		$headers = [ 'AuthToken' => $key, 'Content-Type' => 'application/json' ];
		$args = [ 'body' => $body, 'headers' => $headers, 'method' => $method ];
		$url = wpbme_api::$url . $uri;

		// Perform And Log Transmission
		$response = wp_remote_request( $url, $args );
		wpbme_api::logger( $url, $args, $response );

		// Process Response
		if( is_wp_error( $response ) ) { return $response; }
		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response );

		// Return
		return isset( $response->Response->Data ) ? $response->Response->Data : $response;
	}

	// Log API Communications
	static function logger( $url, $request, $response ) {
		$wpbme_debug = get_option( 'wpbme_debug' );
		if( ! $wpbme_debug ) { return; }
		if( ! function_exists( 'wc_get_logger' ) ) { return; }
		$logger = wc_get_logger();
		$context = [ 'source' => 'benchmark-email-lite' ];
		$request = print_r( $request, true );
		$response = print_r( $response, true );
		$logger->info( "==URL== " . $url, $context );
		$logger->debug( "==REQUEST== " . $request, $context );
		$logger->debug( "==RESPONSE== " . $response, $context );
	}

	// Gets Temporary Token And API Key From User / Pass
	static function authenticate( $user, $pass ) {

		// Get New Temporary Token From User / Pass
		$response = self::benchmark_query(
			'Client/Authenticate',
			'POST',
			[ 'Username' => $user, 'Password' => $pass ]
		);
		if( ! isset( $response->Response->Token ) ) { return; }
		$wpbme_temp_token = trim( $response->Response->Token );

		// Use Temporary Token To Get API Key
		$response = wpbme_api::benchmark_query(
			'Client/Setting', 'GET', null, $wpbme_temp_token
		);
		if( ! isset( $response->Response->Token ) ) { return; }
		$wpbme_key = trim( $response->Response->Token );

		// Use Temporary Token To Get AP Token
		$wpbme_ap_token = self::get_ap_token( $wpbme_temp_token );

		// Return
		return [
			'wpbme_ap_token' => $wpbme_ap_token,
			'wpbme_temp_token' => $wpbme_temp_token,
			'wpbme_key' => $wpbme_key
		];
	}

	// Authenticate And Redirect Benchmark UI
	static function authenticate_ui( $destination_uri ) {

		// Get Maybe Renew Temp Token
		$wpbme_temp_token = self::authenticate_ui_maybe_renew();

		// Authenticate And Get Redirect
		$endpoint = 'https://ui.benchmarkemail.com';
		$url = $endpoint . '/xdc/json/login_redirect_using_token';
		$body = sprintf(
			'token=%s&remember-login=1&redir=%s',
			$wpbme_temp_token,
			urlencode( $destination_uri )
		);
		$args = [ 'body' => $body ];
		$response = wp_remote_post( $url, $args );
		wpbme_api::logger( $url, $args, $response );

		// Process Response
		if( ! is_wp_error( $response ) ) {
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response );
			return empty( $response->redirectURL )
				? false : $endpoint . $response->redirectURL;
		}
	}

	// Maybe Renew Temporary Token
	static function authenticate_ui_maybe_renew() {
		$wpbme_temp_token = get_option( 'wpbme_temp_token' );
		$wpbme_temp_token_ttl = get_option( 'wpbme_temp_token_ttl' );

		// Still Valid
		if( $wpbme_temp_token_ttl >= current_time( 'timestamp' ) ) {
			return $wpbme_temp_token;
		}

		// Renew Now
		$response = wpbme_api::benchmark_query(
			'Client/AuthenticateUseTempToken', 'POST', null, $wpbme_temp_token
		);
		if( empty( $response->Response->Token ) ) { return; }
		$wpbme_temp_token = trim( $response->Response->Token );
		update_option( 'wpbme_temp_token', $wpbme_temp_token );
		update_option( 'wpbme_temp_token_ttl', current_time( 'timestamp' ) + 86400 );
		return $wpbme_temp_token;
	}

	// Get New Automation Pro Token
	static function get_ap_token( $wpbme_temp_token ) {
		$url = 'https://aproapi.benchmarkemail.com/api/v1/token/gettoken';
		$body = 'token=' . $wpbme_temp_token;
		$headers = [
			'Authorization: OAuth ' . $wpbme_temp_token,
			'Content-type: application/x-www-form-urlencoded',
			'Content-length: ' . strlen( $body ),
		];
		//$args = [ 'body' => $body, 'headers' => $headers ];
		//$response = wp_remote_post( $url, $args );
		//return print_r( $response, true );
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		$response = curl_exec( $ch );
		if( ! $response ) { return; }
		$wpbme_ap_token = str_replace( '"', '', trim( $response ) );
		return $wpbme_ap_token;
	}

	// Legacy XML-RPC API
	static function benchmark_query_legacy() {
		require_once( ABSPATH . WPINC . '/class-IXR.php' );
		$url = 'https://api.benchmarkemail.com/1.3/';
		$client = new IXR_Client( $url, false, 443, 15 );
		$args = func_get_args();
		call_user_func_array( [ $client, 'query' ], $args );
		$response = $client->getResponse();
		wpbme_api::logger( $url, $args, $response );
		return $response;
	}
}
