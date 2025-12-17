<?php
/**
 * REST API.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST routes controller.
 */
class MCS_REST {

	/**
	 * Settings helper.
	 *
	 * @var MCS_Settings
	 */
	protected $settings;

	/**
	 * Subscribers helper.
	 *
	 * @var MCS_Subscribers
	 */
	protected $subscribers;

	/**
	 * Constructor.
	 *
	 * @param MCS_Settings    $settings Settings.
	 * @param MCS_Subscribers $subscribers Subs.
	 */
	public function __construct( MCS_Settings $settings, MCS_Subscribers $subscribers ) {
		$this->settings    = $settings;
		$this->subscribers = $subscribers;
	}

	/**
	 * Hook routes.
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			'modern-coming-soon/v1',
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'mode' => array(
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/templates',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_templates' ),
				'permission_callback' => array( $this, 'can_manage' ),
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/bypass/regenerate',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'regenerate_bypass' ),
				'permission_callback' => array( $this, 'can_manage' ),
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/subscribe',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'subscribe' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/subscribers',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list_subscribers' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_subscribers' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/subscribers/export',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_subscribers' ),
				'permission_callback' => array( $this, 'can_manage' ),
			)
		);

		register_rest_route(
			'modern-coming-soon/v1',
			'/upload-html',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'upload_html' ),
				'permission_callback' => array( $this, 'can_manage' ),
				'args'                => array(),
			)
		);
	}

	/**
	 * Permission check.
	 *
	 * @return bool
	 */
	public function can_manage() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get settings.
	 *
	 * @return WP_REST_Response
	 */
	public function get_settings() {
		return new WP_REST_Response( $this->settings->get_all(), 200 );
	}

	/**
	 * Save settings.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function save_settings( WP_REST_Request $request ) {
		$body     = $request->get_json_params();
		$settings = is_array( $body ) ? $body : array();
		$this->settings->update( $settings );
		return new WP_REST_Response( $this->settings->get_all(), 200 );
	}

	/**
	 * Regenerate bypass token.
	 *
	 * @return WP_REST_Response
	 */
	public function regenerate_bypass() {
		$token = $this->settings->regenerate_bypass_token();
		return new WP_REST_Response(
			array(
				'token' => $token,
			),
			200
		);
	}

	/**
	 * Subscription endpoint.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function subscribe( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$email    = sanitize_email( $params['email'] ?? '' );
		$honeypot = sanitize_text_field( $params['hp'] ?? '' );

		if ( ! empty( $honeypot ) ) {
			return new WP_REST_Response( array( 'message' => __( 'Spam detected.', 'modern-coming-soon' ) ), 400 );
		}

		if ( ! is_email( $email ) ) {
			return new WP_REST_Response( array( 'message' => __( 'Invalid email.', 'modern-coming-soon' ) ), 400 );
		}

		$ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$transient_key = 'mcs_rate_' . md5( $ip );
		$count         = (int) get_transient( $transient_key );
		if ( $count > 5 ) {
			return new WP_REST_Response( array( 'message' => __( 'Too many attempts. Please try later.', 'modern-coming-soon' ) ), 429 );
		}
		set_transient( $transient_key, $count + 1, MINUTE_IN_SECONDS * 10 );

		$result = $this->subscribers->insert( $email, $ip, 'form', true );

		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response( array( 'message' => $result->get_error_message() ), 400 );
		}

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * List subscribers.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function list_subscribers( WP_REST_Request $request ) {
		$search = sanitize_text_field( $request->get_param( 'search' ) );
		$page   = (int) $request->get_param( 'page' );
		$per    = (int) $request->get_param( 'per_page' );
		if ( $per <= 0 ) {
			$per = 20;
		}

		$list = $this->subscribers->list( $search, $page ?: 1, $per );
		return new WP_REST_Response( $list, 200 );
	}

	/**
	 * Delete subscribers.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function delete_subscribers( WP_REST_Request $request ) {
		$ids = array_map( 'intval', (array) $request->get_param( 'ids' ) );
		$this->subscribers->delete( $ids );
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Export subscribers.
	 *
	 * @return WP_REST_Response
	 */
	public function export_subscribers() {
		$csv = $this->subscribers->export_csv();
		return new WP_REST_Response(
			array(
				'csv' => base64_encode( $csv ),
			),
			200
		);
	}

	/**
	 * Upload custom HTML file.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function upload_html( WP_REST_Request $request ) {
		if ( empty( $_FILES['file'] ) ) {
			return new WP_REST_Response( array( 'message' => __( 'No file provided.', 'modern-coming-soon' ) ), 400 );
		}

		$file = $_FILES['file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! isset( $file['name'] ) || 'html' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			return new WP_REST_Response( array( 'message' => __( 'Only .html files are allowed.', 'modern-coming-soon' ) ), 400 );
		}

		add_filter( 'upload_mimes', array( $this, 'allow_html_mime' ) );
		$overrides = array(
			'test_form' => false,
			'mimes'     => array( 'html' => 'text/html' ),
		);

		$uploaded = wp_handle_upload( $file, $overrides );
		remove_filter( 'upload_mimes', array( $this, 'allow_html_mime' ) );

		if ( isset( $uploaded['error'] ) ) {
			return new WP_REST_Response( array( 'message' => $uploaded['error'] ), 400 );
		}

		$content = file_get_contents( $uploaded['file'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false !== strpos( $content, '<?php' ) ) {
			@unlink( $uploaded['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
			return new WP_REST_Response( array( 'message' => __( 'PHP code is not allowed in uploaded HTML.', 'modern-coming-soon' ) ), 400 );
		}

		if ( ! file_exists( MCS_UPLOAD_DIR ) ) {
			wp_mkdir_p( MCS_UPLOAD_DIR );
		}

		$dest = trailingslashit( MCS_UPLOAD_DIR ) . basename( $uploaded['file'] );
		copy( $uploaded['file'], $dest ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_copy
		@unlink( $uploaded['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink

		$options                = $this->settings->get_all();
		$options['custom_html'] = basename( $dest );
		$this->settings->update( $options );

		return new WP_REST_Response(
			array(
				'file' => basename( $dest ),
				'url'  => trailingslashit( MCS_UPLOAD_URL ) . basename( $dest ),
			),
			200
		);
	}

	/**
	 * Allow HTML MIME.
	 *
	 * @param array $mimes Existing.
	 * @return array
	 */
	public function allow_html_mime( $mimes ) {
		$mimes['html'] = 'text/html';
		return $mimes;
	}

	/**
	 * Templates meta listing.
	 *
	 * @return WP_REST_Response
	 */
	public function get_templates() {
		$templates_dir = trailingslashit( MCS_PLUGIN_DIR . 'templates' );
		$list          = array();

		foreach ( glob( $templates_dir . '*/meta.json' ) as $meta_file ) { // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.glob
			$meta = json_decode( file_get_contents( $meta_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( ! is_array( $meta ) ) {
				continue;
			}

			$slug = sanitize_key( $meta['slug'] ?? basename( dirname( $meta_file ) ) );
			$name = $meta['name'] ?? $slug;
			if ( is_array( $name ) ) {
				$locale = determine_locale();
				$name   = $name[ $locale ] ?? ( $name['fa'] ?? ( $name['en'] ?? reset( $name ) ) );
			}

			$thumbnail = '';
			if ( ! empty( $meta['thumbnail'] ) ) {
				$thumbnail = trailingslashit( MCS_PLUGIN_URL . 'templates/' . basename( dirname( $meta_file ) ) ) . ltrim( $meta['thumbnail'], '/' );
			}

			$list[] = array(
				'slug'       => $slug,
				'name'       => $name,
				'category'   => $meta['category'] ?? '',
				'thumbnail'  => $thumbnail,
				'preview'    => ! empty( $meta['preview'] ) ? trailingslashit( MCS_PLUGIN_URL . 'templates/' . basename( dirname( $meta_file ) ) ) . ltrim( $meta['preview'], '/' ) : $thumbnail,
				'description'=> $meta['description'] ?? '',
				'tags'       => $meta['tags'] ?? array(),
			);
		}

		if ( empty( $list ) ) {
			$file = MCS_PLUGIN_DIR . 'templates/templates.json';
			if ( file_exists( $file ) ) {
				$json = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$list = json_decode( $json, true );
			}
		}

		return new WP_REST_Response( $list, 200 );
	}
}
