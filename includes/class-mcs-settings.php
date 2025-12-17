<?php
/**
 * Settings helper.
 *
 * @package ModernComingSoon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings container and helpers.
 */
class MCS_Settings {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	protected $option_name = 'mcs_options';

	/**
	 * Get defaults.
	 *
	 * @return array
	 */
	public function defaults() {
		$is_fa = 'fa_IR' === determine_locale();

		return array(
			'mode'                 => 'disabled',
			'status_code'          => 200,
			'status_code_factory'  => 503,
			'status_code_redirect' => 302,
			'retry_after'          => 3600,
			'noindex'              => true,
			'noindex_coming'       => false,
			'block_rest_factory'   => true,
			'block_xmlrpc'         => false,
			'bypass_roles'         => array( 'administrator' ),
			'bypass_ips'           => array(),
			'bypass_urls'          => array(),
			'bypass_token_hash'    => '',
			'bypass_token_enabled' => false,
			'allow_registered'     => false,
			'template'             => 'classic',
			'custom_html'          => '',
			'logo'                 => '',
			'logo_id'              => 0,
			'title'                => $is_fa ? 'به زودی' : __( 'Coming Soon', 'modern-coming-soon' ),
			'subtitle'             => $is_fa ? 'در حال آماده‌سازی تجربه‌ای نو هستیم.' : __( 'We are building something great.', 'modern-coming-soon' ),
			'content'              => $is_fa ? 'لطفاً ایمیل خود را وارد کنید تا زمان آماده شدن به شما اطلاع دهیم.' : '',
			'background'           => array(
				'type'  => 'color',
				'value' => '#101010',
			),
			'button_label'         => $is_fa ? 'خبرم کن' : __( 'Notify Me', 'modern-coming-soon' ),
			'button_url'           => '#notify',
			'button_color'         => '#0ea5e9',
			'title_size'           => 40,
			'social'               => array(),
			'countdown'            => array(
				'enabled' => false,
				'date'    => '',
				'timezone'=> get_option( 'timezone_string', 'UTC' ),
			),
			'progress'             => array(
				'enabled' => false,
				'value'   => 40,
			),
			'sections'             => array(
				'logo'      => true,
				'title'     => true,
				'subtitle'  => true,
				'content'   => true,
				'countdown' => false,
				'progress'  => false,
				'subscribe' => true,
				'social'    => true,
			),
			'typography'           => array(
				'font_family' => 'Vazir, Tahoma, sans-serif',
				'rtl'         => function_exists( 'is_rtl' ) ? is_rtl() : false,
			),
			'seo_description'      => '',
		);
	}

	/**
	 * Return option array merged with defaults.
	 *
	 * @return array
	 */
	public function get_all() {
		$value = get_option( $this->option_name, array() );
		return wp_parse_args( $value, $this->defaults() );
	}

	/**
	 * Persist options.
	 *
	 * @param array $value Settings.
	 */
	public function update( $value ) {
		$clean = $this->sanitize( $value );
		update_option( $this->option_name, $clean, false );
	}

	/**
	 * Ensure defaults exist.
	 */
	public function maybe_create_defaults() {
		if ( ! get_option( $this->option_name, false ) ) {
			update_option( $this->option_name, $this->defaults(), false );
		}
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $value Raw.
	 * @return array
	 */
	public function sanitize( $value ) {
		$defaults = $this->defaults();
		$clean    = wp_parse_args( $value, $defaults );

		$clean['mode'] = in_array( $clean['mode'], array( 'disabled', 'coming_soon', 'maintenance', 'factory' ), true ) ? $clean['mode'] : 'disabled';

		$clean['status_code']          = (int) $clean['status_code'];
		$clean['status_code_factory']  = (int) $clean['status_code_factory'];
		$clean['status_code_redirect'] = (int) $clean['status_code_redirect'];
		$clean['retry_after']          = max( 0, (int) $clean['retry_after'] );

		$clean['noindex']            = (bool) $clean['noindex'];
		$clean['noindex_coming']     = (bool) $clean['noindex_coming'];
		$clean['block_rest_factory'] = (bool) $clean['block_rest_factory'];
		$clean['block_xmlrpc']       = (bool) $clean['block_xmlrpc'];
		$clean['allow_registered']   = (bool) $clean['allow_registered'];

		$clean['bypass_roles'] = array_values(
			array_filter(
				array_map( 'sanitize_key', (array) $clean['bypass_roles'] ),
				'strlen'
			)
		);

		$clean['bypass_ips'] = array_values(
			array_filter(
				array_map( 'sanitize_text_field', (array) $clean['bypass_ips'] ),
				'strlen'
			)
		);

		$clean['bypass_urls'] = array_values(
			array_filter(
				array_map( 'sanitize_text_field', (array) $clean['bypass_urls'] ),
				'strlen'
			)
		);

		$clean['bypass_token_enabled'] = (bool) $clean['bypass_token_enabled'];
		$clean['bypass_token_hash']    = sanitize_text_field( $clean['bypass_token_hash'] );

		$clean['template']    = sanitize_key( $clean['template'] );
		$clean['custom_html'] = sanitize_text_field( $clean['custom_html'] );
		$clean['logo']        = esc_url_raw( $clean['logo'] );
		$clean['logo_id']     = (int) $clean['logo_id'];
		$clean['title']       = sanitize_text_field( $clean['title'] );
		$clean['subtitle']    = sanitize_text_field( $clean['subtitle'] );
		$clean['content']     = wp_kses_post( $clean['content'] );
		$clean['button_color']= sanitize_hex_color( $clean['button_color'] ) ?: '#0ea5e9';
		$clean['title_size']  = max( 20, min( 72, (int) $clean['title_size'] ) );

		$background = is_array( $clean['background'] ) ? $clean['background'] : array();
		$clean['background'] = array(
			'type'  => in_array( $background['type'] ?? '', array( 'color', 'gradient', 'image' ), true ) ? $background['type'] : 'color',
			'value' => sanitize_text_field( $background['value'] ?? '' ),
		);

		$clean['button_label'] = sanitize_text_field( $clean['button_label'] );
		$clean['button_url']   = esc_url_raw( $clean['button_url'] );

		$clean['social'] = array();
		if ( isset( $value['social'] ) && is_array( $value['social'] ) ) {
			foreach ( $value['social'] as $row ) {
				if ( empty( $row['label'] ) || empty( $row['url'] ) ) {
					continue;
				}
				$clean['social'][] = array(
					'label' => sanitize_text_field( $row['label'] ),
					'url'   => esc_url_raw( $row['url'] ),
				);
			}
		}

		$countdown = is_array( $value['countdown'] ?? null ) ? $value['countdown'] : array();
		$clean['countdown'] = array(
			'enabled'  => ! empty( $countdown['enabled'] ),
			'date'     => sanitize_text_field( $countdown['date'] ?? '' ),
			'timezone' => sanitize_text_field( $countdown['timezone'] ?? get_option( 'timezone_string', 'UTC' ) ),
		);

		$progress = is_array( $value['progress'] ?? null ) ? $value['progress'] : array();
		$clean['progress'] = array(
			'enabled' => ! empty( $progress['enabled'] ),
			'value'   => max( 0, min( 100, (int) ( $progress['value'] ?? 0 ) ) ),
		);

		$sections = is_array( $value['sections'] ?? null ) ? $value['sections'] : array();
		$clean['sections'] = array(
			'logo'      => ! empty( $sections['logo'] ?? $defaults['sections']['logo'] ),
			'title'     => ! empty( $sections['title'] ?? $defaults['sections']['title'] ),
			'subtitle'  => ! empty( $sections['subtitle'] ?? $defaults['sections']['subtitle'] ),
			'content'   => ! empty( $sections['content'] ?? $defaults['sections']['content'] ),
			'countdown' => ! empty( $sections['countdown'] ?? $defaults['sections']['countdown'] ),
			'progress'  => ! empty( $sections['progress'] ?? $defaults['sections']['progress'] ),
			'subscribe' => ! empty( $sections['subscribe'] ?? $defaults['sections']['subscribe'] ),
			'social'    => ! empty( $sections['social'] ?? $defaults['sections']['social'] ),
		);

		$typography = is_array( $value['typography'] ?? null ) ? $value['typography'] : array();
		$clean['typography'] = array(
			'font_family' => sanitize_text_field( $typography['font_family'] ?? 'Arial, sans-serif' ),
			'rtl'         => ! empty( $typography['rtl'] ),
		);

		$clean['seo_description'] = sanitize_text_field( $value['seo_description'] );

		return $clean;
	}

	/**
	 * Generate and store new bypass token hash, return plain token for display.
	 *
	 * @return string
	 */
	public function regenerate_bypass_token() {
		$token = wp_generate_password( 20, false, false );
		$hash  = wp_hash_password( $token );

		$options                    = $this->get_all();
		$options['bypass_token_hash']    = $hash;
		$options['bypass_token_enabled'] = true;
		$this->update( $options );

		return $token;
	}

	/**
	 * Get bypass token display value (masked).
	 *
	 * @return string
	 */
	public function get_bypass_token_display() {
		$options = $this->get_all();
		if ( empty( $options['bypass_token_hash'] ) ) {
			return '';
		}

		return '********';
	}
}
