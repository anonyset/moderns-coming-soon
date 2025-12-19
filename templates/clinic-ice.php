<?php
/**
 * Clinic Ice template.
 *
 * @package ModernComingSoon
 */

$options = $data['options'];
$rtl     = ! empty( $data['rtl'] );
$dir     = $rtl ? 'rtl' : 'ltr';

$title_size = ! empty( $options['title_size'] ) ? (int) $options['title_size'] : 42;
$btn_color  = ! empty( $options['button_color'] ) ? $options['button_color'] : '#3bc6ff';

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
		body.mcs-clinic-ice{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08), transparent 30%),radial-gradient(circle at 80% 10%, rgba(59,198,255,0.2), transparent 30%),#061019;color:#e8f7ff;font-family:<?php echo esc_attr( $options['typography']['font_family'] ); ?>;}
		.mcs-shell{width:100%;max-width:900px;padding:52px 40px;border-radius:28px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);backdrop-filter: blur(10px);box-shadow:0 20px 70px rgba(0,0,0,0.35);}
		.mcs-top{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;}
		.mcs-logo img{max-height:70px;}
		.mcs-chip{padding:10px 16px;border-radius:999px;background:rgba(59,198,255,0.14);color:#dff7ff;font-weight:700;font-size:13px;letter-spacing:0.5px;}
		.mcs-title{font-size:<?php echo esc_attr( $title_size ); ?>px;margin:22px 0 12px;font-weight:800;line-height:1.2;}
		.mcs-subtitle{margin:0 0 14px;font-size:18px;opacity:0.9;}
		.mcs-content{margin:0 0 18px;font-size:16px;line-height:1.7;opacity:0.95;}
		.mcs-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start;}
		.mcs-card{padding:16px 18px;border-radius:14px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);} 
		.mcs-countdown{display:flex;gap:10px;}
		.mcs-countdown .item{flex:1;text-align:center;background:rgba(255,255,255,0.08);padding:12px;border-radius:12px;}
		.mcs-countdown .value{font-size:22px;font-weight:700;}
		.mcs-countdown .label{opacity:0.8;font-size:13px;}
		.mcs-progress .bar{height:10px;background:rgba(255,255,255,0.16);border-radius:20px;overflow:hidden;}
		.mcs-progress .fill{height:10px;background:linear-gradient(90deg,#3bc6ff,#6ff0d9);}
		.mcs-subscribe{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
		.mcs-subscribe input[type="email"]{flex:1;min-width:220px;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.25);background:rgba(255,255,255,0.08);color:#e8f7ff;}
		.mcs-subscribe button{padding:12px 18px;border-radius:12px;border:none;background:<?php echo esc_attr( $btn_color ); ?>;color:#012030;font-weight:800;cursor:pointer;box-shadow:0 10px 30px rgba(59,198,255,0.35);}
		.mcs-social{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
		.mcs-social a{color:#e8f7ff;text-decoration:none;border:1px solid rgba(255,255,255,0.14);padding:10px 12px;border-radius:12px;opacity:0.9;}
		.mcs-message{color:#6ff0d9;font-size:14px;margin-top:6px;}
		.mcs-honeypot{position:absolute;left:-9999px;opacity:0;}
		@media(max-width:800px){.mcs-grid{grid-template-columns:1fr;}.mcs-shell{padding:34px 24px;}.mcs-title{font-size:32px;}}
	</style>
</head>
<body class="mcs-clinic-ice" style="<?php echo esc_attr( $bg_style ); ?>">
	<div class="mcs-shell">
		<div class="mcs-top">
			<?php if ( $options['sections']['logo'] && ! empty( $options['logo'] ) ) : ?>
				<div class="mcs-logo"><img src="<?php echo esc_url( $options['logo'] ); ?>" alt="<?php esc_attr_e( 'Logo', 'modern-coming-soon' ); ?>"></div>
			<?php endif; ?>
			<div class="mcs-chip"><?php echo esc_html( $rtl ? 'کلینیک در حال آماده‌سازی است' : 'Clinic relaunch in progress' ); ?></div>
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
			<div class="mcs-card">
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

			<div class="mcs-card">
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
			if(!root) return;
			const end=new Date(root.dataset.date).getTime();
			const update=()=>{
				const now=Date.now();
				let diff=Math.max(0,end-now);
				const days=Math.floor(diff/86400000);diff-=days*86400000;
				const hours=Math.floor(diff/3600000);diff-=hours*3600000;
				const minutes=Math.floor(diff/60000);diff-=minutes*60000;
				const seconds=Math.floor(diff/1000);
				root.querySelector('[data-part="days"]').textContent=String(days).padStart(2,'0');
				root.querySelector('[data-part="hours"]').textContent=String(hours).padStart(2,'0');
				root.querySelector('[data-part="minutes"]').textContent=String(minutes).padStart(2,'0');
				root.querySelector('[data-part="seconds"]').textContent=String(seconds).padStart(2,'0');
			};
			update();setInterval(update,1000);
		})();
	</script>
</body>
</html>
