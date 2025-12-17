<?php
/**
 * Frontend mode handling.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend gatekeeper.
 */
class MCS_Frontend {

	/**
	 * Settings helper.
	 *
	 * @var MCS_Settings
	 */
	protected $settings;

	/**
	 * Subscribers handler.
	 *
	 * @var MCS_Subscribers
	 */
	protected $subscribers;

	/**
	 * Constructor.
	 *
	 * @param MCS_Settings    $settings Settings.
	 * @param MCS_Subscribers $subscribers Subscribers.
	 */
	public function __construct( MCS_Settings $settings, MCS_Subscribers $subscribers ) {
		$this->settings    = $settings;
		$this->subscribers = $subscribers;
	}

	/**
	 * Hook into WordPress.
	 */
	public function hooks() {
		add_action( 'template_redirect', array( $this, 'maybe_render_mode' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
		add_action( 'wp_head', array( $this, 'maybe_noindex' ) );
		add_filter( 'xmlrpc_enabled', array( $this, 'maybe_block_xmlrpc' ) );
		add_filter( 'rest_authentication_errors', array( $this, 'maybe_block_rest_requests' ) );
	}

	/**
	 * Enqueue public assets.
	 */
	public function enqueue_frontend() {
		$options = $this->settings->get_all();
		if ( 'disabled' === $options['mode'] ) {
			return;
		}

		wp_enqueue_style(
			'modern-coming-soon-frontend',
			MCS_PLUGIN_URL . 'assets/frontend/style.css',
			array(),
			MCS_VERSION
		);

		wp_enqueue_script(
			'modern-coming-soon-frontend',
			MCS_PLUGIN_URL . 'assets/frontend/frontend.js',
			array( 'jquery' ),
			MCS_VERSION,
			true
		);

		wp_localize_script(
			'modern-coming-soon-frontend',
			'mcsFrontend',
			array(
				'restUrl' => esc_url_raw( rest_url( 'modern-coming-soon/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Add meta robots based on mode.
	 */
	public function maybe_noindex() {
		$options = $this->settings->get_all();
		if ( 'disabled' === $options['mode'] ) {
			return;
		}

		if ( $this->should_bypass( $options ) ) {
			return;
		}

		$noindex = $options['noindex'] || ( 'coming_soon' === $options['mode'] && $options['noindex_coming'] ) || ( 'maintenance' === $options['mode'] );
		if ( $noindex ) {
			echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
		}

		if ( ! empty( $options['seo_description'] ) ) {
			echo '<meta name="description" content="' . esc_attr( $options['seo_description'] ) . '" />' . "\n";
		}
	}

	/**
	 * Maybe block XML-RPC when in factory mode.
	 *
	 * @param bool $enabled Enabled flag.
	 * @return bool
	 */
	public function maybe_block_xmlrpc( $enabled ) {
		$options = $this->settings->get_all();
		if ( 'factory' === $options['mode'] && $options['block_xmlrpc'] ) {
			return false;
		}
		return $enabled;
	}

	/**
	 * Decide whether visitor should bypass lock.
	 *
	 * @param array $options Options.
	 * @return bool
	 */
	protected function should_bypass( $options ) {
		$previewing = isset( $_GET['csmm_preview'] ) && current_user_can( 'manage_options' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $previewing ) {
			return false;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return true;
		}

		if ( wp_doing_cron() ) {
			return true;
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( user_can( $user, 'manage_options' ) ) {
				return true;
			}

			if ( ! empty( $options['allow_registered'] ) ) {
				return true;
			}

			foreach ( (array) $user->roles as $role ) {
				if ( in_array( $role, (array) $options['bypass_roles'], true ) ) {
					return true;
				}
			}
		}

		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( $ip && $this->ip_allowed( $ip, (array) $options['bypass_ips'] ) ) {
			return true;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		foreach ( (array) $options['bypass_urls'] as $pattern ) {
			$pattern = trim( $pattern );
			if ( '' === $pattern ) {
				continue;
			}

			if ( $pattern === $request_uri ) {
				return true;
			}

			if ( @preg_match( '/' . str_replace( '/', '\/', $pattern ) . '/', $request_uri ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
				return true;
			}
		}

		// Secret token via query or cookie.
		$token = isset( $_GET['mcs_bypass'] ) ? sanitize_text_field( wp_unslash( $_GET['mcs_bypass'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $token ) && isset( $_COOKIE['mcs_bypass'] ) ) {
			$token = sanitize_text_field( wp_unslash( $_COOKIE['mcs_bypass'] ) );
		}

		if ( $token && ! empty( $options['bypass_token_hash'] ) && ! empty( $options['bypass_token_enabled'] ) && wp_check_password( $token, $options['bypass_token_hash'] ) ) {
			setcookie( 'mcs_bypass', $token, time() + MONTH_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
			return true;
		}

		return false;
	}

	/**
	 * CIDR aware IP matching.
	 *
	 * @param string $ip   IP.
	 * @param array  $list CIDR list.
	 * @return bool
	 */
	protected function ip_allowed( $ip, $list ) {
		foreach ( $list as $rule ) {
			$rule = trim( $rule );
			if ( '' === $rule ) {
				continue;
			}

			if ( false === strpos( $rule, '/' ) && $ip === $rule ) {
				return true;
			}

			if ( false !== strpos( $rule, '/' ) && $this->cidr_match( $ip, $rule ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Match CIDR.
	 *
	 * @param string $ip   IP.
	 * @param string $cidr CIDR.
	 * @return bool
	 */
	protected function cidr_match( $ip, $cidr ) {
		list( $subnet, $mask ) = explode( '/', $cidr );
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) && filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$ip_long    = ip2long( $ip );
			$subnet_long = ip2long( $subnet );
			$mask_long  = -1 << ( 32 - (int) $mask );
			$subnet_long &= $mask_long;
			return ( $ip_long & $mask_long ) === $subnet_long;
		}

		return false;
	}

	/**
	 * Should block REST based on mode.
	 *
	 * @param array $options Options.
	 * @return bool
	 */
	protected function should_block_rest( $options ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			if ( 'factory' === $options['mode'] && ! empty( $options['block_rest_factory'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Block REST calls in factory mode for non-admins.
	 *
	 * @param WP_Error|bool|null $result Current result.
	 * @return WP_Error|bool|null
	 */
	public function maybe_block_rest_requests( $result ) {
		$options = $this->settings->get_all();
		if ( 'factory' !== $options['mode'] || empty( $options['block_rest_factory'] ) ) {
			return $result;
		}

		if ( $this->should_bypass( $options ) ) {
			return $result;
		}

		return new WP_Error( 'mcs_rest_blocked', __( 'REST API blocked by Factory mode.', 'modern-coming-soon' ), array( 'status' => (int) $options['status_code_factory'] ?: 503 ) );
	}

	/**
	 * Render maintenance/coming soon page when needed.
	 */
	public function maybe_render_mode() {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$options = $this->settings->get_all();

		if ( 'disabled' === $options['mode'] ) {
			return;
		}

		$previewing = isset( $_GET['csmm_preview'] ) && current_user_can( 'manage_options' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Allow Elementor/Gutenberg preview and REST editing.
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['mcs-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( $this->should_bypass( $options ) ) {
			return;
		}

		if ( $this->should_block_rest( $options ) ) {
			status_header( (int) $options['status_code_factory'] );
			wp_send_json_error(
				array(
					'message' => __( 'Access blocked by Factory mode.', 'modern-coming-soon' ),
				)
			);
		}

		$code = 200;
		switch ( $options['mode'] ) {
			case 'coming_soon':
				$code = (int) $options['status_code'] ?: 200;
				break;
			case 'maintenance':
				$code = 503;
				break;
			case 'factory':
				$code = (int) $options['status_code_factory'] ?: 503;
				break;
		}

		if ( $previewing ) {
			nocache_headers();
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Pragma: no-cache' );
		}

		status_header( $code );

		if ( 'maintenance' === $options['mode'] || 'factory' === $options['mode'] ) {
			$retry = (int) $options['retry_after'];
			if ( $retry > 0 ) {
				header( 'Retry-After: ' . $retry );
			}
		}

		if ( 'coming_soon' === $options['mode'] && 302 === (int) $options['status_code'] ) {
			header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		}

		// Override template via preview query.
		if ( $previewing && isset( $_GET['tpl'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$options['template'] = sanitize_key( wp_unslash( $_GET['tpl'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$this->render_page( $options );
		exit;
	}

	/**
	 * Render chosen template or uploaded HTML.
	 *
	 * @param array $options Options.
	 */
	protected function render_page( $options ) {
		if ( ! empty( $options['custom_html'] ) ) {
			$file = trailingslashit( MCS_UPLOAD_DIR ) . basename( $options['custom_html'] );
			if ( file_exists( $file ) ) {
				$content = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}
		}

		$template = sanitize_key( $options['template'] );
		$file     = MCS_PLUGIN_DIR . 'templates/' . $template . '.php';
		if ( ! file_exists( $file ) ) {
			$file = MCS_PLUGIN_DIR . 'templates/classic.php';
		}

		$rtl = $options['typography']['rtl'] || ( function_exists( 'is_rtl' ) && is_rtl() );

		$data = array(
			'mode'        => $options['mode'],
			'options'     => $options,
			'rtl'         => $rtl,
			'upload_url'  => MCS_UPLOAD_URL,
			'template_id' => $template,
		);

		include $file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}
}
