=== Modern Coming Soon & Maintenance ===
Contributors: hosein-momeni
Tags: coming soon, maintenance mode, elementor, gutenberg, countdown
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Modern coming soon, maintenance, and factory (hard lock) modes with React admin, template library, Gutenberg blocks, Elementor widgets, and email capture.

== Description ==

Bring your site online with confidence using a bilingual (fa_IR/en_US) coming soon and maintenance toolkit. Includes:

* Modes: Coming Soon (200/302), Maintenance (503 + Retry-After), Factory/Hard Lock (503/403, REST/XML-RPC blocking)
* Bypass controls: admin bypass, role allowlist, IP allowlist (CIDR), URL allowlist (regex), secret token (`?mcs_bypass=TOKEN`)
* Templates: local library, RTL friendly, customizable logo, content, countdown, progress bar, social links
* Custom HTML upload (strict .html MIME, saved in uploads)
* Email capture: custom table, honeypot, rate limiting, CSV export
* React admin app via WP REST API
* Gutenberg blocks: subscription form, countdown
* Elementor widgets: subscription form, countdown
* SEO: noindex toggles, status code control

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/modern-coming-soon/`.
2. Activate through **Plugins**.
3. Open **Coming Soon** menu to configure modes, design, and access rules.

== Frequently Asked Questions ==

= How do I bypass maintenance? =
Administrators always bypass. Add extra roles/IPs or use `?mcs_bypass=TOKEN` generated in Access Rules.

= Does it block the REST API? =
Only Factory mode can block REST. Cron and admin tools remain functional.

= Where are emails stored? =
In the `wp_mcs_subscribers` table. Export from the **Subscribers** tab (REST endpoint: `/modern-coming-soon/v1/subscribers/export`).

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
First release of the modern coming soon, maintenance, and factory mode toolkit.
