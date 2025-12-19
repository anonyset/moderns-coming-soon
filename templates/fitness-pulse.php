<?php
/**
 * Fitness Pulse template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';

$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 44;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#16f5a6';

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
		body.mcs-fitness-pulse{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0d0f1a,#10172a),radial-gradient(circle at 20% 20%,rgba(22,245,166,0.2),transparent 35%),radial-gradient(circle at 80% 10%,rgba(252,57,133,0.25),transparent 35%);color:#f5f7fb;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;}
		.mcs-container{width:100%;max-width:1100px;padding:50px 40px;border-radius:26px;background:rgba(17,21,35,0.9);border:1px solid rgba(255,255,255,0.05);box-shadow:0 25px 70px rgba(0,0,0,0.45);} 
		.mcs-tag{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;background:rgba(22,245,166,0.14);color:#16f5a6;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:1px;}
		.mcs-title{font-size:<?php echo esc_attr( $title_size ); ?>px;margin:18px 0 10px;font-weight:900;line-height:1.1;}
		.mcs-subtitle{margin:0 0 12px;font-size:18px;opacity:0.9;}
		.mcs-content{margin:0 0 20px;font-size:16px;line-height:1.7;opacity:0.92;}
		.mcs-grid{display:grid;grid-template-columns:1.1fr 0.9fr;gap:20px;align-items:start;}
		.mcs-panel{padding:18px;border-radius:16px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);} 
		.mcs-countdown{display:flex;gap:12px;flex-wrap:wrap;}
		.mcs-countdown .item{flex:1 1 120px;text-align:center;padding:14px;border-radius:14px;background:rgba(255,255,255,0.05);} 
		.mcs-countdown .value{font-size:26px;font-weight:900;color:#16f5a6;}
		.mcs-countdown .label{opacity:0.75;font-size:13px;}
		.mcs-progress .bar{height:12px;background:rgba(255,255,255,0.08);border-radius:30px;overflow:hidden;}
		.mcs-progress .fill{height:12px;background:linear-gradient(90deg,#fc3985,#16f5a6);} 
		.mcs-subscribe{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;}
		.mcs-subscribe input[type="email"]{flex:1;min-width:240px;padding:14px;border-radius:14px;border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.07);color:#f5f7fb;font-weight:600;}
		.mcs-subscribe button{padding:14px 18px;border-radius:14px;border:none;background:<?php echo esc_attr( $btn_color ); ?>;color:#0b111f;font-weight:900;cursor:pointer;box-shadow:0 14px 30px rgba(22,245,166,0.35);} 
		.mcs-social{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
		.mcs-social a{color:#f5f7fb;text-decoration:none;border:1px solid rgba(255,255,255,0.14);padding:10px 12px;border-radius:12px;}
		.mcs-message{color:#16f5a6;font-size:14px;margin-top:6px;}
		.mcs-honeypot{position:absolute;left:-9999px;opacity:0;}
		@media(max-width:860px){.mcs-grid{grid-template-columns:1fr;}.mcs-container{padding:34px 24px;}.mcs-title{font-size:32px;}}
	</style>
</head>
<body class="mcs-fitness-pulse" style="<?php echo esc_attr( $bg_style ); ?>">
	<div class="mcs-container">
		<div class="mcs-tag">
			<span>⚡</span>
			<span><?php echo esc_html( $rtl ? 'باشگاه در حال ارتقا' : 'Studio upgrade' ); ?></span>
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

		<div class="mcs-grid">
			<div class="mcs-panel">
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
						<div class="bar"><div class="fill" style="width: <?php echo esc_attr( (int) $options['progress']['percent'] ); ?>%;"></div></div>
					</div>
				<?php endif; ?>
			</div>

			<div class="mcs-panel">
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
