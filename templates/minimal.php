<?php
/**
 * Minimal template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( $dir ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<style>
		body.mcs-minimal{margin:0;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;background:#0b1b2b;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;}
		.mcs-minimal-inner{text-align:center;padding:30px;max-width:620px;}
		.mcs-minimal-title{font-size:38px;font-weight:800;margin:0 0 12px;}
		.mcs-minimal-subtitle{font-size:18px;opacity:0.9;margin:0 0 18px;}
		.mcs-minimal-btn{display:inline-block;padding:12px 20px;border-radius:30px;border:1px solid #4ade80;color:#4ade80;text-decoration:none;margin-top:10px;}
		.mcs-subscribe-form{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-top:14px;}
		.mcs-subscribe-form input[type="email"]{padding:12px 14px;border-radius:28px;border:1px solid rgba(255,255,255,0.3);background:transparent;color:#fff;min-width:240px;}
		.mcs-subscribe-form button{padding:12px 18px;border-radius:28px;border:none;background:#4ade80;color:#0b1b2b;font-weight:700;cursor:pointer;}
		.mcs-message{margin-top:8px;font-size:14px;color:#4ade80;}
		.mcs-countdown{margin:12px 0;font-weight:700;letter-spacing:1px;}
	</style>
</head>
<body class="mcs-minimal">
	<div class="mcs-minimal-inner">
		<?php if ( $options['sections']['title'] ) : ?>
			<h1 class="mcs-minimal-title"><?php echo esc_html( $options['title'] ); ?></h1>
		<?php endif; ?>
		<?php if ( $options['sections']['subtitle'] ) : ?>
			<p class="mcs-minimal-subtitle"><?php echo esc_html( $options['subtitle'] ); ?></p>
		<?php endif; ?>

		<?php if ( $options['sections']['countdown'] && ! empty( $options['countdown']['date'] ) ) : ?>
			<div class="mcs-countdown" data-date="<?php echo esc_attr( $options['countdown']['date'] ); ?>"></div>
		<?php endif; ?>

		<?php if ( $options['sections']['subscribe'] ) : ?>
			<form class="mcs-subscribe-form" data-source="template-minimal">
				<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required>
				<input type="text" name="hp" class="mcs-honeypot" tabindex="-1" aria-hidden="true">
				<button type="submit"><?php echo esc_html( $options['button_label'] ); ?></button>
				<div class="mcs-message" aria-live="polite"></div>
			</form>
		<?php endif; ?>

		<?php if ( $options['sections']['social'] && ! empty( $options['social'] ) ) : ?>
			<div class="mcs-social">
				<?php foreach ( $options['social'] as $item ) : ?>
					<a class="mcs-minimal-btn" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
<?php wp_footer(); ?>
</body>
</html>
