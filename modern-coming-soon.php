<?php
/**
 * Plugin Name: Modern Coming Soon & Maintenance
 * Plugin URI: https://wordpress.org/plugins/modern-coming-soon
 * Description: Modern Coming Soon, Maintenance, and Factory (Hard Lock) modes with React admin, template library, Gutenberg, and Elementor widgets.
 * Version: 1.0.15
 * Author: Hosein Momeni
 * Author URI: https://qomweb.site/maint
 * Text Domain: modern-coming-soon
 * Domain Path: /languages
 * Update URI: https://github.com/anonyset/modern-coming-soon
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MCS_VERSION', '1.0.15' );
define( 'MCS_PLUGIN_FILE', __FILE__ );
define( 'MCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MCS_UPLOAD_DIR', wp_upload_dir()['basedir'] . '/modern-coming-soon' );
define( 'MCS_UPLOAD_URL', wp_upload_dir()['baseurl'] . '/modern-coming-soon' );

require_once MCS_PLUGIN_DIR . 'includes/class-mcs-plugin.php';

/**
 * Boot plugin.
 *
 * @return Modern_Coming_Soon
 */
function mcs() {
	return Modern_Coming_Soon::instance();
}

mcs();

register_activation_hook( __FILE__, array( 'Modern_Coming_Soon', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Modern_Coming_Soon', 'deactivate' ) );
