<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// ReST API Class
class wpbme_api {

	// Endpoints
	static
		$url_api = 'https://clientapi.benchmarkemail.com/',
		$url_ui = 'https://ui.benchmarkemail.com/',
		$url_apro = 'https://aproapi.benchmarkemail.com/',
		$url_xml = 'https://api.benchmarkemail.com/';

	// Get All Signup Forms
	static function get_forms() {
		return self::benchmark_query( 'SignupForm/' );
	}

	// Get JS Link For Signup Form
	static function get_form_data( $id ) {
		return self::benchmark_query( 'SignupForm/' . $id );
	}

	// Vendor Handshake
	static function update_partner() {
		$uri = 'Client/Partner';
		$body = [ 'PartnerLogin' => 'beautomated' ];
		return self::benchmark_query( $uri, 'POST', $body );
	}

	// Get All Contact Lists
	static function get_lists() {
		return self::benchmark_query( 'Contact/' );
	}

	// Creates Signup Form
	static function create_form( $list_id, $list_name, $form_name, $introduction, $button, $fields ) {

		// Make Form
		$body = [
			'Detail' => [
				'Name' => $form_name,
				'Lists' => [ [ 'ID' => $list_id, 'Name' => $list_name ] ],
				'Version' => 4,
			]
		];
		$response = self::benchmark_query( 'SignupForm/', 'POST', $body );
		$form_id = isset( $response->ID ) ? intval( $response->ID ) : '';
		if( ! $form_id ) { return; }

		// Set Form Details
		$set_fields = [];
		foreach( $fields as $i => $field ) {
			$set_fields[] = [
				'Column' => $i,
				'Name' => $field['name'],
				'Label' => $field['label'],
				'IsEmail' => ( $field['name'] == 'Email' ) ? 1 : 0,
				'IsRequired' => $field['required'] ? 1 : 0,
				'Type' => 1, //( $field['name'] == 'Email' ) ? 'Email' : 'Text',
				//'Order' => $i,
			];
		}
		$body = [
			'ID' => $form_id,
			'Detail' => [
				'Button' => $button,
				'Fields' => $set_fields,
				'Introduction' => $introduction,
			]
		];
		$response = self::benchmark_query( 'SignupForm/' . $form_id, 'PATCH', $body );
		return $form_id;
	}

	// Creates Email Campaign
	static function create_email( $name, $subject, $from_name, $from_email, $post_id='' ) {
		$uri = 'Emails/';
		$lists = self::get_lists();
		if( ! is_array( $lists ) ) { return; }
		$to_lists = [];
		$protected_lists = [
			'Master Unsubscribe List',
			'WooCommerce Abandoned Carts',
			'WooCommerce Customers'
		];
		foreach( $lists as $list ) {
			if( empty( $list->ID ) ) { continue; }
			if( in_array( $list->Name, $protected_lists ) ) { continue; }
			$to_lists[] = [ 'ID' => $list->ID ];
		}
		$body = [
			'Detail' => [
				'Version' => 420,
				'EmailType' => 'DD',
				'LayoutID' => 10,
				//'HasPermissionReminderMessage' => 1,
				'HasWebpageVersion' => 1,
				'Name' => $name,
				'Subject' => $subject,
				'FromName' => $from_name,
				'FromEmail' => $from_email,
				'ReplyEmail' => $from_email,
				'ContactLists' => $to_lists,
				'IsRSS'	=> 1,
				'RSSURL' => strstr( get_permalink( $post_id ), 'localhost' )
					? 'http://woocommerce.com/blog/feed/?withoutcomments=1'
					: get_permalink( $post_id ) . 'feed/?withoutcomments=1',
				//'RSSSchedule'	=> '',
				//'RSSinterval'	=> '',
				//'IsRSSActive' => 1,
				//'TemplateContent' => $content,
				//'TemplateText' => $content,
				//'IsManualText' => 1,
				//'TemplateCode' => $content,
			]
		];
		return self::benchmark_query( $uri, 'POST', $body );
	}

	// Talk To Benchmark ReST API
	static function benchmark_query( $uri = '', $method = 'GET', $body = null, $key = null ) {

		// Organize Request
		if( $body ) { $body = json_encode( $body ); }
		$key = $key ? $key : get_option( 'wpbme_key' );
		$headers = [ 'AuthToken' => $key, 'Content-Type' => 'application/json' ];
		$args = [ 'body' => $body, 'headers' => $headers, 'method' => $method ];
		$url = self::$url_api . $uri;

		// Perform And Log Transmission
		$response = wp_remote_request( $url, $args );
		self::logger( $url, $args, $response );

		// Process Response
		if( is_wp_error( $response ) ) { return $response; }
		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response );

		// Handle Errors
		if( isset( $response->Response->Error ) && is_array( $response->Response->Error ) ) {
			$errors = '';
			foreach( $response->Response->Error as $error ) {
				$errors .= isset( $error->Message ) ? $error->Message : '';
				$errors .= ' ';
			}
			$errors = trim( $errors );
			if( $errors ) { return $errors; }
		}

		// Return Success
		return isset( $response->Response->Data ) ? $response->Response->Data : $response;
	}

	// Log API Communications
	static function logger( $url, $request, $response ) {
		$wpbme_debug = get_option( 'wpbme_debug' );
		if( ! $wpbme_debug ) { return; }
		if( ! function_exists( 'wc_get_logger' ) ) { return; }
		$logger = wc_get_logger();
		$details = sprintf(
			"==URL==\n%s\n\n==REQUEST==\n%s\n\n==RESPONSE==\n%s",
			$url,
			print_r( $request, true ),
			print_r( $response, true )
		);
		$logger->info( $details, [ 'source' => 'benchmark-email-lite' ] );
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
		$response = self::benchmark_query(
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
	static function authenticate_ui_redirect( $destination_uri ) {
		$wpbme_temp_token = get_option( 'wpbme_temp_token' );
		$wpbme_temp_token_ttl = get_option( 'wpbme_temp_token_ttl' );

		// Maybe Refresh Auth Token
		if( $wpbme_temp_token_ttl < current_time( 'timestamp' ) ) {
			self::authenticate_ui_renew();
		}

		// Request UI Auth Redirect
		$url = self::$url_ui . 'xdc/json/login_redirect_using_token';
		$body = sprintf(
			'token=%s&remember-login=1&redir=%s',
			$wpbme_temp_token,
			urlencode( $destination_uri )
		);
		$args = [ 'body' => $body ];
		$response = wp_remote_post( $url, $args );
		self::logger( $url, $args, $response );

		// Process Response
		if( ! is_wp_error( $response ) ) {
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response );
			return empty( $response->redirectURL )
				? false : untrailingslashit( self::$url_ui ) . $response->redirectURL;
		}
	}

	// Maybe Renew Temporary Token
	static function authenticate_ui_renew() {
		$wpbme_temp_token = get_option( 'wpbme_temp_token' );
		$response = self::benchmark_query(
			'Client/AuthenticateUseTempToken', 'POST', null, $wpbme_temp_token
		);

		// Handle Error
		if( empty( $response->Response->Token ) ) {
			delete_option( 'wpbme_temp_token' );
			delete_option( 'wpbme_temp_token_ttl' );
			return;
		}

		// Success
		$wpbme_temp_token = trim( $response->Response->Token );
		update_option( 'wpbme_temp_token', $wpbme_temp_token );
		update_option( 'wpbme_temp_token_ttl', current_time( 'timestamp' ) + 86400 );
		return $wpbme_temp_token;
	}

	// Get New Automation Pro Token
	static function get_ap_token( $wpbme_temp_token ) {
		$url = self::$url_apro . 'api/v1/token/gettoken';
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
		$url = self::$url_xml . '1.3/';
		$client = new IXR_Client( $url, false, 443, 15 );
		$args = func_get_args();
		call_user_func_array( [ $client, 'query' ], $args );
		$response = $client->getResponse();
		self::logger( $url, $args, $response );
		return $response;
	}
}
