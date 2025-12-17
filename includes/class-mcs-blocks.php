<?php
/**
 * Gutenberg blocks.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block registration.
 */
class MCS_Blocks {

	/**
	 * Settings.
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
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
	}

	/**
	 * Register blocks and scripts.
	 */
	public function register_blocks() {
		wp_register_script(
			'mcs-blocks-subscription',
			MCS_PLUGIN_URL . 'blocks/subscription-form.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
			MCS_VERSION,
			true
		);

		wp_register_script(
			'mcs-blocks-countdown',
			MCS_PLUGIN_URL . 'blocks/countdown.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
			MCS_VERSION,
			true
		);

		register_block_type(
			'modern-coming-soon/subscription',
			array(
				'editor_script'   => 'mcs-blocks-subscription',
				'render_callback' => array( $this, 'render_subscription' ),
				'attributes'      => array(
					'title' => array(
						'type'    => 'string',
						'default' => __( 'Stay in the loop', 'modern-coming-soon' ),
					),
					'subtitle' => array(
						'type'    => 'string',
						'default' => __( 'Join our list for updates.', 'modern-coming-soon' ),
					),
				),
			)
		);

		register_block_type(
			'modern-coming-soon/countdown',
			array(
				'editor_script'   => 'mcs-blocks-countdown',
				'render_callback' => array( $this, 'render_countdown' ),
				'attributes'      => array(
					'date' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);
	}

	/**
	 * Frontend assets for blocks.
	 */
	public function enqueue_block_assets() {
		wp_enqueue_style(
			'mcs-blocks-style',
			MCS_PLUGIN_URL . 'assets/frontend/blocks.css',
			array(),
			MCS_VERSION
		);
	}

	/**
	 * Render subscription form block.
	 *
	 * @param array $attrs Attributes.
	 * @return string
	 */
	public function render_subscription( $attrs ) {
		$title    = ! empty( $attrs['title'] ) ? wp_kses_post( $attrs['title'] ) : '';
		$subtitle = ! empty( $attrs['subtitle'] ) ? wp_kses_post( $attrs['subtitle'] ) : '';

		ob_start();
		?>
		<div class="mcs-subscribe-block">
			<?php if ( $title ) : ?>
				<h3><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p><?php echo $subtitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<form class="mcs-subscribe-form" data-source="block">
				<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required />
				<input type="text" name="hp" class="mcs-honeypot" tabindex="-1" aria-hidden="true" />
				<button type="submit"><?php esc_html_e( 'Notify me', 'modern-coming-soon' ); ?></button>
				<div class="mcs-message" aria-live="polite"></div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render countdown block markup.
	 *
	 * @param array $attrs Attributes.
	 * @return string
	 */
	public function render_countdown( $attrs ) {
		$date = ! empty( $attrs['date'] ) ? esc_attr( $attrs['date'] ) : '';
		ob_start();
		?>
		<div class="mcs-countdown-block" data-date="<?php echo $date; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			<span class="mcs-countdown-label"><?php esc_html_e( 'Countdown', 'modern-coming-soon' ); ?></span>
			<div class="mcs-countdown-values" aria-live="polite"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
