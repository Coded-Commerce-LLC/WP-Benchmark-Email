<?php
/**
 * Plugin Name: Benchmark Email Lite
 * Plugin URI: https://codedcommerce.com/product/benchmark-email-lite
 * Description: Connects WordPress with Benchmark Email for newsletter sign-up forms and post-to-email campaigns.
 * Version: 3.0-alpha
 * Author: Coded Commerce, LLC
 * Author URI: https://codedcommerce.com
 * Developer: Sean Conklin
 * Developer URI: https://seanconklin.wordpress.com
 * Text Domain: benchmark-email-lite
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
require_once( 'class.widget.php' );
