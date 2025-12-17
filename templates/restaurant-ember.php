<?php
/**
 * Restaurant Ember template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';
$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 42;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#eab308';
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( $dir ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css');
		body.mcs-restaurant{margin:0;background:linear-gradient(160deg,#0f0a0a 0%,#1f0f0f 60%,#3d1b10 100%);color:#f5e7da;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;min-height:100vh;}
		.mcs-rest-wrap{max-width:980px;margin:0 auto;padding:60px 22px;display:grid;gap:26px;}
		.mcs-rest-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:24px;backdrop-filter:blur(10px);}
		.mcs-rest-title{font-size:<?php echo esc_attr( $title_size ); ?>px;margin:0 0 10px;}
		.mcs-rest-sub{font-size:18px;opacity:0.9;margin:0 0 14px;}
		.mcs-rest-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;}
		.mcs-countdown{display:flex;gap:8px;margin-top:10px;}
		.mcs-countdown .item{flex:1;text-align:center;border-radius:12px;padding:10px;background:rgba(0,0,0,0.35);border:1px solid rgba(255,255,255,0.1);}
		.mcs-subscribe-form{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;}
		.mcs-subscribe-form input[type="email"]{flex:1;min-width:220px;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.2);background:rgba(0,0,0,0.25);color:#fff;}
		.mcs-subscribe-form button{padding:12px 16px;border:none;border-radius:12px;background:<?php echo esc_attr( $btn_color ); ?>;color:#0f0a0a;font-weight:800;cursor:pointer;}
		.mcs-social{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
		.mcs-social a{padding:8px 12px;border-radius:10px;border:1px solid rgba(255,255,255,0.2);color:#f5e7da;text-decoration:none;}
		.mcs-badge{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:10px;background:rgba(255,255,255,0.08);font-size:12px;letter-spacing:1px;}
		@media(max-width:620px){.mcs-rest-title{font-size:30px;}}
	</style>
</head>
<body class="mcs-restaurant">
	<div class="mcs-rest-wrap">
		<div class="mcs-rest-card">
			<div class="mcs-badge"><?php esc_html_e( 'آشپزخانه در حال آماده‌سازی است', 'modern-coming-soon' ); ?></div>
			<?php if ( $options['sections']['title'] ) : ?>
				<h1 class="mcs-rest-title"><?php echo esc_html( $options['title'] ); ?></h1>
			<?php endif; ?>
			<?php if ( $options['sections']['subtitle'] ) : ?>
				<p class="mcs-rest-sub"><?php echo esc_html( $options['subtitle'] ); ?></p>
			<?php endif; ?>
			<?php if ( $options['sections']['content'] && ! empty( $options['content'] ) ) : ?>
				<div class="wp-block-paragraph"><?php echo wp_kses_post( wpautop( $options['content'] ) ); ?></div>
			<?php endif; ?>
			<?php if ( $options['sections']['countdown'] && ! empty( $options['countdown']['date'] ) ) : ?>
				<div class="mcs-countdown" data-date="<?php echo esc_attr( $options['countdown']['date'] ); ?>">
					<div class="item"><div class="value" data-part="days">00</div><div class="label"><?php esc_html_e( 'Days', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="hours">00</div><div class="label"><?php esc_html_e( 'Hours', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="minutes">00</div><div class="label"><?php esc_html_e( 'Minutes', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="seconds">00</div><div class="label"><?php esc_html_e( 'Seconds', 'modern-coming-soon' ); ?></div></div>
				</div>
			<?php endif; ?>
			<?php if ( $options['sections']['subscribe'] ) : ?>
				<form class="mcs-subscribe-form" data-source="template-restaurant">
					<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required>
					<input type="text" name="hp" class="mcs-honeypot" tabindex="-1" aria-hidden="true">
					<button type="submit"><?php echo esc_html( $options['button_label'] ); ?></button>
					<div class="mcs-message" aria-live="polite"></div>
				</form>
			<?php endif; ?>
			<?php if ( $options['sections']['social'] && ! empty( $options['social'] ) ) : ?>
				<div class="mcs-social">
					<?php foreach ( $options['social'] as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php wp_footer(); ?>
</body>
</html>
