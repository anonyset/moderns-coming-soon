<?php
/**
 * Subscriber storage handler.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage subscriber records.
 */
class MCS_Subscribers {

	/**
	 * Table name helper.
	 *
	 * @return string
	 */
	protected function table() {
		global $wpdb;
		return $wpdb->prefix . 'mcs_subscribers';
	}

	/**
	 * Create table schema.
	 */
	public function create_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();
		$table   = $this->table();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			email varchar(190) NOT NULL,
			created_at datetime NOT NULL,
			ip varchar(45) DEFAULT '' NOT NULL,
			source varchar(50) DEFAULT '' NOT NULL,
			consent tinyint(1) DEFAULT 0 NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY email (email)
		) {$charset};";

		dbDelta( $sql );
	}

	/**
	 * Insert subscriber row.
	 *
	 * @param string $email Email.
	 * @param string $ip    IP address.
	 * @param string $source Source identifier.
	 * @param bool   $consent Consent flag.
	 * @return bool|\WP_Error
	 */
	public function insert( $email, $ip, $source = 'form', $consent = false ) {
		global $wpdb;

		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'mcs_invalid_email', __( 'Invalid email address.', 'modern-coming-soon' ) );
		}

		$table = $this->table();

		$inserted = $wpdb->insert(
			$table,
			array(
				'email'      => $email,
				'created_at' => current_time( 'mysql' ),
				'ip'         => sanitize_text_field( $ip ),
				'source'     => sanitize_text_field( $source ),
				'consent'    => $consent ? 1 : 0,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			)
		);

		if ( false === $inserted ) {
			return new WP_Error( 'mcs_insert_failed', __( 'Could not save subscriber.', 'modern-coming-soon' ) );
		}

		return true;
	}

	/**
	 * Query subscribers.
	 *
	 * @param string $search Search term.
	 * @param int    $paged  Page.
	 * @param int    $per    Per page.
	 * @return array
	 */
	public function list( $search = '', $paged = 1, $per = 20 ) {
		global $wpdb;
		$table  = $this->table();
		$offset = ( max( 1, (int) $paged ) - 1 ) * $per;

		$where  = '';
		$params = array();
		if ( ! empty( $search ) ) {
			$where    = 'WHERE email LIKE %s';
			$params[] = '%' . $wpdb->esc_like( $search ) . '%';
		}

		$sql   = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$query = $wpdb->prepare( $sql, array_merge( $params, array( $per, $offset ) ) );
		$rows  = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$count_sql = "SELECT COUNT(*) FROM {$table} {$where}";
		$count_q   = $wpdb->prepare( $count_sql, $params );
		$total     = (int) $wpdb->get_var( $count_q ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return array(
			'items' => $rows,
			'total' => $total,
		);
	}

	/**
	 * Delete by ids.
	 *
	 * @param array $ids IDs.
	 */
	public function delete( $ids ) {
		global $wpdb;
		$table = $this->table();
		foreach ( (array) $ids as $id ) {
			$wpdb->delete( $table, array( 'id' => (int) $id ), array( '%d' ) );
		}
	}

	/**
	 * Export rows to CSV string.
	 *
	 * @return string
	 */
	public function export_csv() {
		global $wpdb;
		$table = $this->table();
		$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared

		$fh = fopen( 'php://temp', 'w+' );
		fputcsv( $fh, array( 'id', 'email', 'created_at', 'ip', 'source', 'consent' ) );
		foreach ( (array) $rows as $row ) {
			fputcsv(
				$fh,
				array(
					$row['id'],
					$row['email'],
					$row['created_at'],
					$row['ip'],
					$row['source'],
					$row['consent'],
				)
			);
		}
		rewind( $fh );
		$content = stream_get_contents( $fh );
		fclose( $fh );
		return $content;
	}

	/**
	 * Count subscribers.
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;
		$table = $this->table();
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
	}
}
