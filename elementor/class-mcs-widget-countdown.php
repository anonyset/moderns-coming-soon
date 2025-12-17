<?php
/**
 * Elementor Countdown widget.
 *
 * @package ModernComingSoon
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Countdown widget.
 */
class MCS_Widget_Countdown extends Widget_Base {

	/**
	 * Settings helper.
	 *
	 * @var MCS_Settings
	 */
	protected $settings_helper;

	/**
	 * Constructor.
	 *
	 * @param MCS_Settings $settings_helper Settings.
	 * @param array        $data            Data.
	 * @param mixed        $args            Args.
	 */
	public function __construct( MCS_Settings $settings_helper, $data = array(), $args = null ) {
		$this->settings_helper = $settings_helper;
		parent::__construct( $data, $args );
	}

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mcs-countdown';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Coming Soon Countdown', 'modern-coming-soon' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-countdown';
	}

	/**
	 * Categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Scripts.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'mcs-elementor' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Countdown', 'modern-coming-soon' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'date',
			array(
				'label'   => __( 'Date', 'modern-coming-soon' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => '',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$date     = ! empty( $settings['date'] ) ? $settings['date'] : '';
		?>
		<div class="mcs-countdown-block" data-date="<?php echo esc_attr( $date ); ?>">
			<span class="mcs-countdown-label"><?php esc_html_e( 'Countdown', 'modern-coming-soon' ); ?></span>
			<div class="mcs-countdown-values" aria-live="polite"></div>
		</div>
		<?php
	}
}
