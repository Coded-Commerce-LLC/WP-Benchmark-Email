<?php
/**
 * Plugin Name: Benchmark Email Lite
 * Plugin URI: https://www.benchmarkemail.com
 * Description: Connects WordPress with Benchmark Email for newsletter sign-up forms and post-to-email campaigns.
 * Version: 3.3
 * Author: Coded Commerce, LLC
 * Author URI: https://codedcommerce.com
 * Developer: Sean Conklin
 * Developer URI: https://seanconklin.wordpress.com
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Include Object Files
require_once( 'class.admin.php' );
require_once( 'class.api.php' );
require_once( 'class.cron.php' );
require_once( 'class.frontend.php' );
require_once( 'class.settings.php' );
require_once( 'class.sister.php' );
require_once( 'class.upgrade.php' );
require_once( 'class.widget.php' );
