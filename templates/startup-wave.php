<?php
/**
 * Startup Wave template (Gutenberg-friendly markup).
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';
$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 48;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#06b6d4';
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( $dir ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		@import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css');
		body.mcs-startup{margin:0;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;background:radial-gradient(circle at 20% 20%,#1e293b,#0f172a 40%);color:#e2e8f0;min-height:100vh;}
		.mcs-startup-wrap{max-width:1100px;margin:0 auto;padding:60px 24px;}
		.mcs-hero{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:center;gap:24px;}
		.mcs-badge{display:inline-flex;gap:10px;align-items:center;border-radius:999px;background:rgba(255,255,255,0.08);padding:8px 14px;font-size:12px;letter-spacing:0.5px;text-transform:uppercase;}
		.mcs-title{font-size:<?php echo esc_attr( $title_size ); ?>px;line-height:1.1;margin:12px 0 8px;}
		.mcs-subtitle{font-size:18px;opacity:0.9;margin:0 0 18px;}
		.mcs-cta{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
		.mcs-cta button{background:<?php echo esc_attr( $btn_color ); ?>;border:none;color:#fff;padding:14px 20px;border-radius:14px;font-weight:700;cursor:pointer;}
		.mcs-cta a{color:#8b5cf6;text-decoration:none;font-weight:700;}
		.mcs-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.05);padding:18px;border-radius:18px;backdrop-filter:blur(8px);}
		.mcs-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-top:18px;}
		.mcs-countdown{display:flex;gap:10px;}
		.mcs-countdown .item{flex:1;text-align:center;padding:12px;border-radius:12px;background:rgba(0,0,0,0.25);}
		.mcs-subscribe-form{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;}
		.mcs-subscribe-form input[type="email"]{flex:1;min-width:220px;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.05);color:#fff;}
		.mcs-subscribe-form button{padding:12px 18px;border-radius:12px;border:none;background:#06b6d4;color:#0b1221;font-weight:800;cursor:pointer;}
		.mcs-social{display:flex;gap:10px;flex-wrap:wrap;}
		.mcs-social a{padding:8px 12px;border-radius:12px;background:rgba(255,255,255,0.08);color:#fff;text-decoration:none;}
		@media(max-width:640px){.mcs-title{font-size:32px;}}
	</style>
</head>
<body class="mcs-startup">
	<div class="mcs-startup-wrap">
		<div class="mcs-hero wp-block-group">
			<div>
				<div class="mcs-badge"><?php esc_html_e( 'نسخه جدید در راه است', 'modern-coming-soon' ); ?></div>
				<?php if ( $options['sections']['title'] ) : ?>
					<h1 class="mcs-title"><?php echo esc_html( $options['title'] ); ?></h1>
				<?php endif; ?>
				<?php if ( $options['sections']['subtitle'] ) : ?>
					<p class="mcs-subtitle"><?php echo esc_html( $options['subtitle'] ); ?></p>
				<?php endif; ?>
				<div class="mcs-cta">
					<?php if ( $options['sections']['subscribe'] ) : ?>
						<button onclick="document.querySelector('.mcs-subscribe-form')?.scrollIntoView({behavior:'smooth'})">
							<?php echo esc_html( $options['button_label'] ); ?>
						</button>
					<?php endif; ?>
					<a href="<?php echo esc_url( $options['button_url'] ); ?>"><?php esc_html_e( 'مشاهده جزییات', 'modern-coming-soon' ); ?></a>
				</div>
				<?php if ( $options['sections']['countdown'] && ! empty( $options['countdown']['date'] ) ) : ?>
					<div class="mcs-countdown" data-date="<?php echo esc_attr( $options['countdown']['date'] ); ?>">
						<div class="item"><div class="value" data-part="days">00</div><div class="label"><?php esc_html_e( 'Days', 'modern-coming-soon' ); ?></div></div>
						<div class="item"><div class="value" data-part="hours">00</div><div class="label"><?php esc_html_e( 'Hours', 'modern-coming-soon' ); ?></div></div>
						<div class="item"><div class="value" data-part="minutes">00</div><div class="label"><?php esc_html_e( 'Minutes', 'modern-coming-soon' ); ?></div></div>
						<div class="item"><div class="value" data-part="seconds">00</div><div class="label"><?php esc_html_e( 'Seconds', 'modern-coming-soon' ); ?></div></div>
					</div>
				<?php endif; ?>
			</div>
			<div class="mcs-card">
				<?php if ( $options['sections']['content'] && ! empty( $options['content'] ) ) : ?>
					<div class="wp-block-paragraph"><?php echo wp_kses_post( wpautop( $options['content'] ) ); ?></div>
				<?php endif; ?>
				<?php if ( $options['sections']['subscribe'] ) : ?>
					<form class="mcs-subscribe-form" data-source="template-startup">
						<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required>
						<input type="text" name="hp" class="mcs-honeypot" tabindex="-1" aria-hidden="true">
						<button type="submit" style="background:<?php echo esc_attr( $btn_color ); ?>;"><?php echo esc_html( $options['button_label'] ); ?></button>
						<div class="mcs-message" aria-live="polite"></div>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $options['sections']['social'] && ! empty( $options['social'] ) ) : ?>
			<div class="mcs-grid" style="margin-top:24px;">
				<div class="mcs-card">
					<strong><?php esc_html_e( 'ما را دنبال کنید', 'modern-coming-soon' ); ?></strong>
					<div class="mcs-social">
						<?php foreach ( $options['social'] as $item ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php wp_footer(); ?>
</body>
</html>
