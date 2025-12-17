<?php
/**
 * Main plugin bootstrap.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once MCS_PLUGIN_DIR . 'includes/class-mcs-settings.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-subscribers.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-rest.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-frontend.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-blocks.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-elementor.php';
require_once MCS_PLUGIN_DIR . 'includes/class-mcs-updater.php';

/**
 * Core plugin controller.
 */
class Modern_Coming_Soon {

	/**
	 * Singleton instance.
	 *
	 * @var Modern_Coming_Soon
	 */
	private static $instance;

	/**
	 * Settings helper.
	 *
	 * @var MCS_Settings
	 */
	public $settings;

	/**
	 * Subscribers helper.
	 *
	 * @var MCS_Subscribers
	 */
	public $subscribers;

	/**
	 * REST controller.
	 *
	 * @var MCS_REST
	 */
	public $rest;

	/**
	 * Frontend handler.
	 *
	 * @var MCS_Frontend
	 */
	public $frontend;

	/**
	 * Blocks handler.
	 *
	 * @var MCS_Blocks
	 */
	public $blocks;

	/**
	 * Elementor handler.
	 *
	 * @var MCS_Elementor
	 */
	public $elementor;

	/**
	 * Updater handler.
	 *
	 * @var MCS_Updater
	 */
	public $updater;

	/**
	 * Instantiate singleton.
	 *
	 * @return Modern_Coming_Soon
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->settings    = new MCS_Settings();
		$this->subscribers = new MCS_Subscribers();
		$this->rest        = new MCS_REST( $this->settings, $this->subscribers );
		$this->frontend    = new MCS_Frontend( $this->settings, $this->subscribers );
		$this->blocks      = new MCS_Blocks( $this->settings );
		$this->elementor   = new MCS_Elementor( $this->settings );
		if ( is_admin() ) {
			$this->updater = new MCS_Updater( MCS_PLUGIN_FILE, MCS_VERSION );
			$this->updater->hooks();
		}

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( 'gettext', array( $this, 'fallback_farsi' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( MCS_PLUGIN_FILE ), array( $this, 'plugin_links' ) );

		$this->rest->hooks();
		$this->frontend->hooks();
		$this->blocks->hooks();
		$this->elementor->hooks();
	}

	/**
	 * Lightweight fallback translations when fa_IR mo is missing.
	 *
	 * @param string $translated Translated.
	 * @param string $text       Original.
	 * @param string $domain     Domain.
	 * @return string
	 */
	public function fallback_farsi( $translated, $text, $domain ) {
		if ( 'modern-coming-soon' !== $domain ) {
			return $translated;
		}

		$locale = determine_locale();
		if ( 'fa_IR' !== $locale ) {
			return $translated;
		}

		$map = array(
			'Coming Soon'                               => 'به زودی',
			'Maintenance'                               => 'حالت تعمیر',
			'Maintenance Mode'                          => 'حالت تعمیر',
			'Factory Lock'                              => 'قفل کارخانه',
			'Disabled'                                  => 'غیرفعال',
			'Save'                                      => 'ذخیره',
			'Settings'                                  => 'تنظیمات',
			'Access blocked by Factory mode.'           => 'دسترسی در حالت کارخانه مسدود است.',
			'REST API blocked by Factory mode.'         => 'REST در حالت کارخانه مسدود است.',
			'Invalid email address.'                    => 'ایمیل معتبر نیست.',
			'Too many attempts. Please try later.'      => 'تعداد تلاش زیاد است. کمی بعد دوباره امتحان کنید.',
			'Could not save subscriber.'                => 'ثبت مشترک انجام نشد.',
			'Settings saved.'                           => 'تنظیمات ذخیره شد.',
			'New token: '                               => 'توکن جدید: ',
			'Enable bypass token'                       => 'فعال‌سازی توکن مخفی',
			'Regenerate token'                          => 'تولید توکن جدید',
			'Countdown'                                 => 'شمارش معکوس',
			'Notify me'                                 => 'خبرم کن',
			'Email address'                             => 'آدرس ایمیل',
			'Subscribe'                                 => 'اشتراک',
			'Retry-After: '                             => 'مدت انتظار: ',
			'Save changes'                              => 'ذخیره تغییرات',
			'Saved'                                     => 'ذخیره شد',
			'Preview'                                   => 'پیش‌نمایش',
			'Dashboard'                                 => 'داشبورد',
			'Design'                                    => 'طراحی',
			'Templates'                                 => 'قالب‌ها',
			'Access Rules'                              => 'قوانین دسترسی',
			'Subscribers'                               => 'مشترکین',
			'Advanced'                                  => 'پیشرفته',
			'Select Logo'                               => 'انتخاب لوگو',
			'Remove'                                    => 'حذف',
			'Roles'                                     => 'نقش‌ها',
			'IP allowlist'                              => 'فهرست IP مجاز',
			'URL allowlist'                             => 'فهرست آدرس مجاز',
			'Secret token'                              => 'توکن مخفی',
		);

		if ( isset( $map[ $text ] ) ) {
			return $map[ $text ];
		}

		return $translated;
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'modern-coming-soon', false, dirname( plugin_basename( MCS_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Add admin menu.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Coming Soon', 'modern-coming-soon' ),
			__( 'Coming Soon', 'modern-coming-soon' ),
			'manage_options',
			'modern-coming-soon',
			array( $this, 'render_admin' ),
			'dashicons-visibility',
			58
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Admin hook suffix.
	 */
	public function enqueue_admin( $hook ) {
		if ( 'toplevel_page_modern-coming-soon' !== $hook ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'modern-coming-soon-admin',
			MCS_PLUGIN_URL . 'assets/admin/style.css',
			array( 'wp-components' ),
			MCS_VERSION
		);

		wp_enqueue_script(
			'modern-coming-soon-admin',
			MCS_PLUGIN_URL . 'assets/admin/app.js',
			array( 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'wp-data', 'wp-compose', 'wp-primitives', 'wp-notices', 'wp-block-editor', 'wp-media-utils' ),
			MCS_VERSION,
			true
		);

		wp_set_script_translations( 'modern-coming-soon-admin', 'modern-coming-soon', MCS_PLUGIN_DIR . 'languages' );

		$roles = array();
		if ( function_exists( 'wp_roles' ) ) {
			foreach ( wp_roles()->roles as $key => $role ) {
				$roles[] = array(
					'value' => $key,
					'label' => translate_user_role( $role['name'] ),
				);
			}
		}

		$subs_total = 0;
		if ( method_exists( $this->subscribers, 'count' ) ) {
			$subs_total = $this->subscribers->count();
		}

		wp_localize_script(
			'modern-coming-soon-admin',
			'mcsAdmin',
			array(
				'nonce'           => wp_create_nonce( 'wp_rest' ),
				'restUrl'         => esc_url_raw( rest_url( 'modern-coming-soon/v1' ) ),
				'settings'        => $this->settings->get_all(),
				'i18n'            => array(
					'mode_disabled'    => __( 'Disabled', 'modern-coming-soon' ),
					'mode_coming'      => __( 'Coming Soon', 'modern-coming-soon' ),
					'mode_maintenance' => __( 'Maintenance', 'modern-coming-soon' ),
					'mode_factory'     => __( 'Factory Lock', 'modern-coming-soon' ),
					'save'             => __( 'Save', 'modern-coming-soon' ),
				),
				'bypassToken'     => $this->settings->get_bypass_token_display(),
				'uploadDirectory' => MCS_UPLOAD_URL,
				'pluginUrl'       => MCS_PLUGIN_URL,
				'homeUrl'         => home_url( '/' ),
				'previewUrl'      => add_query_arg(
					array(
						'csmm_preview' => '1',
						'csmm_as'      => 'visitor',
					),
					home_url( '/' )
				),
				'isRTL'           => is_rtl(),
				'locale'          => determine_locale(),
				'roles'           => $roles,
				'subscribers'     => array(
					'total' => $subs_total,
				),
			)
		);
	}

	/**
	 * Render admin root.
	 */
	public function render_admin() {
		echo '<div class="wrap"><div id="modern-coming-soon-admin"></div></div>';
	}

	/**
	 * Plugin action links.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function plugin_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=modern-coming-soon' ) ) . '">' . esc_html__( 'Settings', 'modern-coming-soon' ) . '</a>';
		return $links;
	}

	/**
	 * Activation tasks.
	 */
	public static function activate() {
		$settings = new MCS_Settings();
		$settings->maybe_create_defaults();

		$subscribers = new MCS_Subscribers();
		$subscribers->create_table();

		if ( ! file_exists( MCS_UPLOAD_DIR ) ) {
			wp_mkdir_p( MCS_UPLOAD_DIR );
		}
	}

	/**
	 * Deactivation tasks.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'mcs_cleanup' );
	}
}
