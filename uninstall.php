<?php
/**
 * Uninstall routine.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_name = 'mcs_options';
delete_option( $option_name );
delete_site_option( $option_name );

global $wpdb;
$table = $wpdb->prefix . 'mcs_subscribers';
$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

$upload_dir = wp_upload_dir();
$plugin_dir = trailingslashit( $upload_dir['basedir'] ) . 'modern-coming-soon';

if ( is_dir( $plugin_dir ) ) {
	$files = glob( $plugin_dir . '/*' ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_glob
	if ( $files ) {
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
			}
		}
	}
	@rmdir( $plugin_dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.directory_rmdir
}
