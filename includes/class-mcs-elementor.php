<?php
/**
 * Elementor widgets integration.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor hookup.
 */
class MCS_Elementor {

	/**
	 * Settings helper.
	 *
	 * @var MCS_Settings
	 */
	protected $settings;

	/**
	 * Constructor.
	 *
	 * @param MCS_Settings $settings Settings.
	 */
	public function __construct( MCS_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Register scripts for widgets.
	 */
	public function register_scripts() {
		wp_register_style(
			'mcs-elementor',
			MCS_PLUGIN_URL . 'assets/frontend/elementor.css',
			array(),
			MCS_VERSION
		);
		wp_register_script(
			'mcs-elementor',
			MCS_PLUGIN_URL . 'assets/frontend/frontend.js',
			array( 'jquery' ),
			MCS_VERSION,
			true
		);
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		require_once MCS_PLUGIN_DIR . 'elementor/class-mcs-widget-subscription.php';
		require_once MCS_PLUGIN_DIR . 'elementor/class-mcs-widget-countdown.php';

		$widgets_manager->register( new MCS_Widget_Subscription( $this->settings ) );
		$widgets_manager->register( new MCS_Widget_Countdown( $this->settings ) );
	}
}
