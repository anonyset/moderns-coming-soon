<?php
/**
 * Lawyer Slate template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';

$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 40;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#c8a15a';

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
		body.mcs-lawyer-slate{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0c0d10,#16181f 60%,#0c0d10);color:#e6e1d8;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;}
		.mcs-wrap{max-width:920px;width:100%;padding:56px 42px;border-radius:24px;background:rgba(18,20,27,0.9);border:1px solid rgba(200,161,90,0.18);box-shadow:0 24px 80px rgba(0,0,0,0.5);} 
		.mcs-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;color:#c8a15a;font-weight:700;letter-spacing:0.6px;text-transform:uppercase;font-size:13px;}
		.mcs-divider{flex:1;height:1px;background:linear-gradient(90deg,rgba(200,161,90,0),rgba(200,161,90,0.4),rgba(200,161,90,0));min-width:120px;}
		.mcs-title{font-size:<?php echo esc_attr( $title_size ); ?>px;margin:20px 0 10px;font-weight:800;}
		.mcs-subtitle{margin:0 0 14px;font-size:18px;opacity:0.9;}
		.mcs-content{margin:0 0 20px;font-size:16px;line-height:1.7;opacity:0.92;}
		.mcs-box{padding:18px;border-radius:16px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);}
		.mcs-countdown{display:flex;gap:12px;flex-wrap:wrap;}
		.mcs-countdown .item{flex:1 1 120px;text-align:center;padding:12px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);}
		.mcs-countdown .value{font-size:24px;font-weight:800;color:#c8a15a;}
		.mcs-countdown .label{opacity:0.75;font-size:13px;}
		.mcs-subscribe{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;}
		.mcs-subscribe input[type="email"]{flex:1;min-width:220px;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.18);background:rgba(255,255,255,0.04);color:#e6e1d8;}
		.mcs-subscribe button{padding:12px 18px;border-radius:12px;border:none;background:<?php echo esc_attr( $btn_color ); ?>;color:#0d0f14;font-weight:800;cursor:pointer;}
		.mcs-social{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
		.mcs-social a{color:#e6e1d8;text-decoration:none;border:1px solid rgba(255,255,255,0.14);padding:10px 12px;border-radius:12px;}
		.mcs-message{color:#c8a15a;font-size:14px;margin-top:6px;}
		.mcs-honeypot{position:absolute;left:-9999px;opacity:0;}
		@media(max-width:720px){.mcs-wrap{padding:36px 26px;}.mcs-title{font-size:30px;}}
	</style>
</head>
<body class="mcs-lawyer-slate" style="<?php echo esc_attr( $bg_style ); ?>">
	<div class="mcs-wrap">
		<div class="mcs-meta">
			<?php if ( $options['sections']['logo'] && ! empty( $options['logo'] ) ) : ?>
				<span class="mcs-logo"><img style="max-height:50px;" src="<?php echo esc_url( $options['logo'] ); ?>" alt="<?php esc_attr_e( 'Logo', 'modern-coming-soon' ); ?>"></span>
			<?php endif; ?>
			<span><?php echo esc_html( $rtl ? 'موسسه حقوقی' : 'Law Firm' ); ?></span>
			<div class="mcs-divider"></div>
			<span><?php echo esc_html( $rtl ? 'به‌زودی' : 'Opening soon' ); ?></span>
		</div>

		<?php if ( $options['sections']['title'] ) : ?>
			<h1 class="mcs-title"><?php echo esc_html( $options['title'] ); ?></h1>
		<?php endif; ?>

		<?php if ( $options['sections']['subtitle'] && ! empty( $options['subtitle'] ) ) : ?>
			<p class="mcs-subtitle"><?php echo esc_html( $options['subtitle'] ); ?></p>
		<?php endif; ?>

		<?php if ( $options['sections']['content'] && ! empty( $options['content'] ) ) : ?>
			<div class="mcs-content"><?php echo wp_kses_post( wpautop( $options['content'] ) ); ?></div>
		<?php endif; ?>

		<div class="mcs-box">
			<?php if ( $options['sections']['countdown'] && ! empty( $options['countdown']['date'] ) ) : ?>
				<div class="mcs-countdown" data-date="<?php echo esc_attr( $options['countdown']['date'] ); ?>">
					<div class="item"><div class="value" data-part="days">00</div><div class="label"><?php esc_html_e( 'Days', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="hours">00</div><div class="label"><?php esc_html_e( 'Hours', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="minutes">00</div><div class="label"><?php esc_html_e( 'Minutes', 'modern-coming-soon' ); ?></div></div>
					<div class="item"><div class="value" data-part="seconds">00</div><div class="label"><?php esc_html_e( 'Seconds', 'modern-coming-soon' ); ?></div></div>
				</div>
			<?php endif; ?>

			<?php if ( $options['sections']['subscribe'] ) : ?>
				<form method="post" class="mcs-subscribe-form">
					<div class="mcs-subscribe">
						<input type="hidden" name="mcs_subscribe" value="1">
						<input class="mcs-honeypot" type="text" name="mcs_hp" tabindex="-1" autocomplete="off">
						<input type="email" name="mcs_email" placeholder="<?php esc_attr_e( 'Email address', 'modern-coming-soon' ); ?>" required>
						<button type="submit"><?php esc_html_e( 'Notify me', 'modern-coming-soon' ); ?></button>
					</div>
				</form>
				<div class="mcs-message" data-message style="display:none"></div>
			<?php endif; ?>

			<?php if ( $options['sections']['social'] && ! empty( $options['social'] ) ) : ?>
				<div class="mcs-social">
					<?php foreach ( $options['social'] as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php wp_footer(); ?>
	<script>
		(function(){
			const root=document.querySelector('.mcs-countdown');
			if(!root)return;
			const end=new Date(root.dataset.date).getTime();
			const update=()=>{
				const now=Date.now();
				let diff=Math.max(0,end-now);
				const d=Math.floor(diff/86400000);diff-=d*86400000;
				const h=Math.floor(diff/3600000);diff-=h*3600000;
				const m=Math.floor(diff/60000);diff-=m*60000;
				const s=Math.floor(diff/1000);
				root.querySelector('[data-part="days"]').textContent=String(d).padStart(2,'0');
				root.querySelector('[data-part="hours"]').textContent=String(h).padStart(2,'0');
				root.querySelector('[data-part="minutes"]').textContent=String(m).padStart(2,'0');
				root.querySelector('[data-part="seconds"]').textContent=String(s).padStart(2,'0');
			};
			update();setInterval(update,1000);
		})();
	</script>
</body>
</html>
