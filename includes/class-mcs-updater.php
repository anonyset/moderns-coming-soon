<?php
/**
 * GitHub-based updater.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles pulling update metadata from GitHub releases.
 */
class MCS_Updater {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Plugin basename (dir/file.php).
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $plugin_version;

	/**
	 * GitHub repo in owner/name format.
	 *
	 * @var string
	 */
	private $repo = 'anonyset/modern-coming-soon';

	/**
	 * Cache key.
	 *
	 * @var string
	 */
	private $cache_key = 'mcs_update_release';

	/**
	 * Cache TTL.
	 *
	 * @var int
	 */
	private $cache_ttl;

	/**
	 * Set up.
	 *
	 * @param string $plugin_file    Plugin file path.
	 * @param string $plugin_version Current plugin version.
	 */
	public function __construct( $plugin_file, $plugin_version ) {
		$this->plugin_file    = $plugin_file;
		$this->plugin_slug    = plugin_basename( $plugin_file );
		$this->plugin_version = $plugin_version;
		$this->cache_ttl      = 6 * HOUR_IN_SECONDS;
	}

	/**
	 * Register hooks.
	 */
	public function hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'maybe_set_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_filter( 'upgrader_source_selection', array( $this, 'rename_package' ), 10, 4 );
	}

	/**
	 * Inject update data into the plugins transient.
	 *
	 * @param stdClass $transient Update transient.
	 * @return stdClass
	 */
	public function maybe_set_update( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked[ $this->plugin_slug ] ) ) {
			return $transient;
		}

		$release = $this->get_latest_release();
		if ( ! $release ) {
			return $transient;
		}

		$current_version = $transient->checked[ $this->plugin_slug ];

		if ( version_compare( $release['version'], $current_version, '<=' ) ) {
			return $transient;
		}

		$transient->response[ $this->plugin_slug ] = (object) array(
			'slug'        => dirname( $this->plugin_slug ),
			'plugin'      => $this->plugin_slug,
			'new_version' => $release['version'],
			'package'     => $release['download_url'],
			'url'         => $release['url'],
		);

		return $transient;
	}

	/**
	 * Show plugin details popup for GitHub releases.
	 *
	 * @param mixed  $result Existing result.
	 * @param string $action Action name.
	 * @param object $args   Request args.
	 * @return mixed
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		$slug = dirname( $this->plugin_slug );
		if ( empty( $args->slug ) || $slug !== $args->slug ) {
			return $result;
		}

		$release = $this->get_latest_release();
		if ( ! $release ) {
			return $result;
		}

		$info                = new stdClass();
		$info->name          = __( 'Modern Coming Soon & Maintenance', 'modern-coming-soon' );
		$info->slug          = $slug;
		$info->version       = $release['version'];
		$info->download_link = $release['download_url'];
		$info->last_updated  = $release['published_at'];
		$info->sections      = array(
			'description' => wp_kses_post(
				wpautop(
					__( 'Updates are delivered directly from the GitHub releases for Modern Coming Soon & Maintenance.', 'modern-coming-soon' )
				)
			),
			'changelog'   => ! empty( $release['body'] )
				? wp_kses_post( wpautop( esc_html( $release['body'] ) ) )
				: wp_kses_post( wpautop( __( 'See the GitHub repository for release details.', 'modern-coming-soon' ) ) ),
		);
		$info->homepage = 'https://github.com/' . $this->repo;
		$info->author   = '<a href="https://qomweb.site/maint">Hosein Momeni</a>';
		$info->requires = '6.0';
		$info->tested   = '6.6';

		return $info;
	}

	/**
	 * Rename GitHub package folder to the expected plugin directory.
	 *
	 * @param string       $source        Source path.
	 * @param string       $remote_source Remote source.
	 * @param WP_Upgrader  $upgrader      Upgrader instance.
	 * @param array|object $hook_extra    Extra data.
	 * @return string
	 */
	public function rename_package( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( empty( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->plugin_slug ) {
			return $source;
		}

		$expected = dirname( $this->plugin_slug );
		if ( '.' === $expected || empty( $expected ) ) {
			return $source;
		}

		if ( basename( $source ) === $expected ) {
			return $source;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem || ! method_exists( $wp_filesystem, 'move' ) ) {
			return $source;
		}

		$new_source = trailingslashit( dirname( $source ) ) . $expected;

		if ( $wp_filesystem->move( $source, $new_source, true ) ) {
			return $new_source;
		}

		return $source;
	}

	/**
	 * Fetch latest GitHub release (cached).
	 *
	 * @return array|false
	 */
	private function get_latest_release() {
		$cached = get_site_transient( $this->cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://api.github.com/repos/' . $this->repo . '/releases/latest',
			array(
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'modern-coming-soon-updater',
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			set_site_transient( $this->cache_key, false, HOUR_IN_SECONDS );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['tag_name'] ) ) {
			set_site_transient( $this->cache_key, false, HOUR_IN_SECONDS );
			return false;
		}

		$tag           = $body['tag_name'];
		$version       = ltrim( $tag, 'vV' );
		$download_url  = sprintf( 'https://github.com/%1$s/archive/refs/tags/%2$s.zip', $this->repo, rawurlencode( $tag ) );
		$release_notes = isset( $body['body'] ) ? $body['body'] : '';
		$published_at  = isset( $body['published_at'] ) ? $body['published_at'] : '';

		$release = array(
			'tag'          => $tag,
			'version'      => $version,
			'download_url' => $download_url,
			'url'          => isset( $body['html_url'] ) ? $body['html_url'] : 'https://github.com/' . $this->repo,
			'body'         => $release_notes,
			'published_at' => $published_at,
		);

		set_site_transient( $this->cache_key, $release, $this->cache_ttl );

		return $release;
	}
}
