<?php
/**
 * Elementor Subscription widget.
 *
 * @package ModernComingSoon
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Subscription form widget.
 */
class MCS_Widget_Subscription extends Widget_Base {

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
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mcs-subscription';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Coming Soon Subscription', 'modern-coming-soon' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-mail';
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
				'label' => __( 'Content', 'modern-coming-soon' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'modern-coming-soon' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Stay in the loop', 'modern-coming-soon' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'modern-coming-soon' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get notified when we launch.', 'modern-coming-soon' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="mcs-subscribe-block">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3><?php echo esc_html( $settings['title'] ); ?></h3>
			<?php endif; ?>
			<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
				<p><?php echo esc_html( $settings['subtitle'] ); ?></p>
			<?php endif; ?>
			<form class="mcs-subscribe-form" data-source="elementor">
				<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required />
				<input type="text" name="hp" class="mcs-honeypot" tabindex="-1" aria-hidden="true" />
				<button type="submit"><?php esc_html_e( 'Notify me', 'modern-coming-soon' ); ?></button>
				<div class="mcs-message" aria-live="polite"></div>
			</form>
		</div>
		<?php
	}
}
