<?php
/**
 * Plugin Name: WP Benchmark Email
 * Plugin URI: https://codedcommerce.com/product/wp-benchmark-email
 * Description: Connects WordPress with Benchmark Email for newsletter sign-up forms and post-to-email campaigns.
 * Version: 3.0-alpha
 * Author: Coded Commerce, LLC
 * Author URI: https://codedcommerce.com
 * Developer: Sean Conklin
 * Developer URI: https://seanconklin.wordpress.com
 * Text Domain: wp-benchmark-email
 * Domain Path: /languages
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Include Object Files
require_once( 'class.admin.php' );
require_once( 'class.api.php' );
require_once( 'class.frontend.php' );
require_once( 'class.settings.php' );

// Front End Hooks
add_action( 'wp_enqueue_scripts', [ 'wpbme_frontend', 'wp_enqueue_scripts' ] );

// Admin Hooks
add_action( 'admin_enqueue_scripts', [ 'wpbme_admin', 'admin_enqueue_scripts' ] );
add_action( 'wp_dashboard_setup', [ 'wpbme_admin', 'wp_dashboard_setup' ] );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ 'wpbme_admin', 'plugin_action_links' ] );

// Settings Hooks
add_action( 'admin_menu', [ 'wpbme_settings', 'admin_menu' ] );

// AJAX Hooks
add_action( 'wp_ajax_wpbme_action', [ 'wpbme_frontend', 'wp_ajax__wpbme_action' ] );
//add_action( 'wp_ajax_nopriv_wpbme_action', [ 'wpbme_frontend', 'wp_ajax__wpbme_action' ] );

// Internationalization
add_action( 'plugins_loaded',  [ 'wpbme_frontend', 'plugins_loaded' ] );
