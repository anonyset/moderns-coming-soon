<?php
/**
 * Classic template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';

$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 40;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#22d3ee';

$bg_style = '';
if ( 'color' === $options['background']['type'] ) {
	$bg_style = 'background:' . esc_attr( $options['background']['value'] );
} elseif ( 'gradient' === $options['background']['type'] ) {
	$bg_style = 'background-image:' . esc_attr( $options['background']['value'] );
} elseif ( 'image' === $options['background']['type'] ) {
	$bg_style = 'background-image:url(' . esc_url( $options['background']['value'] ) . ');background-size:cover;background-position:center;';
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( $dir ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css');
		body.mcs-classic{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#111;color:#fff;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;}
		.mcs-card{max-width:720px;width:100%;padding:48px 36px;background:rgba(0,0,0,0.45);backdrop-filter: blur(6px);border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.35);}
		.mcs-logo img{max-height:70px;}
		.mcs-title{font-size:<?php echo esc_attr( $title_size ); ?>px;margin:16px 0 8px;font-weight:700;}
		.mcs-subtitle{font-size:18px;opacity:0.9;margin:0 0 12px;}
		.mcs-content{font-size:16px;line-height:1.6;opacity:0.95;margin-bottom:16px;}
		.mcs-countdown{display:flex;gap:10px;margin:18px 0;}
		.mcs-countdown .item{flex:1;text-align:center;background:rgba(255,255,255,0.08);padding:12px;border-radius:12px;}
		.mcs-progress{margin:14px 0;}
		.mcs-progress .bar{height:10px;background:rgba(255,255,255,0.2);border-radius:20px;overflow:hidden;}
		.mcs-progress .fill{height:10px;background:linear-gradient(90deg,#7c3aed,#22d3ee);}
		.mcs-subscribe-form{display:flex;gap:10px;flex-wrap:wrap;margin:14px 0;}
		.mcs-subscribe-form input[type="email"]{flex:1;min-width:220px;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.04);color:#fff;}
		.mcs-subscribe-form button{padding:12px 18px;border-radius:12px;border:none;background:<?php echo esc_attr( $btn_color ); ?>;color:#0c0c0c;font-weight:700;cursor:pointer;}
		.mcs-social{display:flex;gap:10px;margin-top:8px;flex-wrap:wrap;}
		.mcs-social a{color:#fff;opacity:0.85;text-decoration:none;padding:8px 12px;border:1px solid rgba(255,255,255,0.15);border-radius:10px;}
		.mcs-message{color:#22d3ee;font-size:14px;margin-top:6px;}
		.mcs-honeypot{position:absolute;left:-9999px;opacity:0;}
		@media(max-width:600px){.mcs-title{font-size:28px;}.mcs-card{padding:32px 24px;}}
	</style>
</head>
<body class="mcs-classic" style="<?php echo esc_attr( $bg_style ); ?>">
	<div class="mcs-card">
		<?php if ( $options['sections']['logo'] && ! empty( $options['logo'] ) ) : ?>
			<div class="mcs-logo"><img src="<?php echo esc_url( $options['logo'] ); ?>" alt="<?php esc_attr_e( 'Logo', 'modern-coming-soon' ); ?>"></div>
		<?php endif; ?>

		<?php if ( $options['sections']['title'] ) : ?>
			<h1 class="mcs-title"><?php echo esc_html( $options['title'] ); ?></h1>
		<?php endif; ?>

		<?php if ( $options['sections']['subtitle'] && ! empty( $options['subtitle'] ) ) : ?>
			<p class="mcs-subtitle"><?php echo esc_html( $options['subtitle'] ); ?></p>
		<?php endif; ?>

		<?php if ( $options['sections']['content'] && ! empty( $options['content'] ) ) : ?>
			<div class="mcs-content"><?php echo wp_kses_post( wpautop( $options['content'] ) ); ?></div>
		<?php endif; ?>

		<?php if ( $options['sections']['countdown'] && ! empty( $options['countdown']['date'] ) ) : ?>
			<div class="mcs-countdown" data-date="<?php echo esc_attr( $options['countdown']['date'] ); ?>">
				<div class="item"><div class="value" data-part="days">00</div><div class="label"><?php esc_html_e( 'Days', 'modern-coming-soon' ); ?></div></div>
				<div class="item"><div class="value" data-part="hours">00</div><div class="label"><?php esc_html_e( 'Hours', 'modern-coming-soon' ); ?></div></div>
				<div class="item"><div class="value" data-part="minutes">00</div><div class="label"><?php esc_html_e( 'Minutes', 'modern-coming-soon' ); ?></div></div>
				<div class="item"><div class="value" data-part="seconds">00</div><div class="label"><?php esc_html_e( 'Seconds', 'modern-coming-soon' ); ?></div></div>
			</div>
		<?php endif; ?>

		<?php if ( $options['sections']['progress'] && ! empty( $options['progress']['enabled'] ) ) : ?>
			<div class="mcs-progress">
				<div class="bar">
					<div class="fill" style="width:<?php echo esc_attr( $options['progress']['value'] ); ?>%"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $options['sections']['subscribe'] ) : ?>
			<form class="mcs-subscribe-form" data-source="template">
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
<?php wp_footer(); ?>
</body>
</html>
