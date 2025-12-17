/**
 * Modern admin app source (build with @wordpress/scripts).
 */
import domReady from '@wordpress/dom-ready';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardHeader,
	CardBody,
	TextControl,
	TextareaControl,
	ToggleControl,
	Notice,
	Spinner,
	SearchControl,
	RangeControl,
	ColorPicker,
	CheckboxControl,
	Modal,
} from '@wordpress/components';
import { MediaUpload } from '@wordpress/block-editor';
import { useState, useEffect, useMemo, useRef, createElement as h } from '@wordpress/element';
import { dispatch } from '@wordpress/data';

const isFa = (window.mcsAdmin?.locale || '').toLowerCase().startsWith('fa');
const T = (en, fa) => (isFa ? fa : en);

const TABS = [
	{ id: 'dashboard', label: T('Dashboard', 'داشبورد'), icon: 'dashboard' },
	{ id: 'design', label: T('Design', 'طراحی'), icon: 'art' },
	{ id: 'templates', label: T('Templates', 'قالب‌ها'), icon: 'screenoptions' },
	{ id: 'access', label: T('Access Rules', 'قوانین دسترسی'), icon: 'shield' },
	{ id: 'subscribers', label: T('Subscribers', 'مشترکین'), icon: 'email-alt' },
	{ id: 'advanced', label: T('Advanced', 'پیشرفته'), icon: 'admin-settings' },
];

const MODE_OPTIONS = [
	{ label: T('Off', 'خاموش'), value: 'disabled' },
	{ label: T('Coming Soon', 'به زودی'), value: 'coming_soon' },
	{ label: T('Maintenance', 'نگهداری'), value: 'maintenance' },
	{ label: T('Factory', 'قفل کارخانه'), value: 'factory' },
];

const templatesCache = new Map();

function useSettings() {
	const [settings, setSettings] = useState(null);
	const [initial, setInitial] = useState(null);
	const [loading, setLoading] = useState(true);
	const [saving, setSaving] = useState(false);

	useEffect(() => {
		apiFetch({ path: '/modern-coming-soon/v1/settings' })
			.then((resp) => {
				setSettings(resp);
				setInitial(resp);
			})
			.catch((err) => dispatch('core/notices').createErrorNotice(err.message || __('Load failed', 'modern-coming-soon')))
			.finally(() => setLoading(false));
	}, []);

	const dirty = useMemo(() => JSON.stringify(settings) !== JSON.stringify(initial), [settings, initial]);

	const save = () => {
		if (!settings) {
			return;
		}
		setSaving(true);
		apiFetch({ path: '/modern-coming-soon/v1/settings', method: 'POST', data: settings })
			.then((resp) => {
				setSettings(resp);
				setInitial(resp);
				dispatch('core/notices').createSuccessNotice(__('Saved', 'modern-coming-soon'));
			})
			.catch((err) => dispatch('core/notices').createErrorNotice(err.message || __('Save failed', 'modern-coming-soon')))
			.finally(() => setSaving(false));
	};

	return { settings, setSettings, loading, dirty, save, saving };
}

function HeaderBar({ mode, onModeChange, dirty, onSave, saving, previewUrl }) {
	return h('div', { className: 'mcs-header' },
	h('div', { className: 'mcs-header__title' },
		h('div', { className: 'mcs-title' }, T('Modern Coming Soon', 'حالت‌های به زودی و تعمیر')),
		h('div', { className: 'mcs-subtitle' }, T('Control Coming Soon, Maintenance, and Factory modes.', 'کنترل صفحه به زودی، تعمیر و قفل کارخانه'))
	),
		h('div', { className: 'mcs-header__actions' },
			h('div', { className: 'mcs-mode-switch' },
				MODE_OPTIONS.map((opt) =>
					h(Button, {
						key: opt.value,
						isSmall: true,
						variant: mode === opt.value ? 'primary' : 'secondary',
						onClick: () => onModeChange(opt.value),
					}, opt.label)
				)
			),
			h('span', { className: 'mcs-status ' + (mode === 'disabled' ? 'is-inactive' : 'is-active') }, mode === 'disabled' ? __('Inactive', 'modern-coming-soon') : __('Active', 'modern-coming-soon')),
			h(Button, { variant: 'secondary', onClick: () => window.open(previewUrl, '_blank') }, __('Preview', 'modern-coming-soon')),
			h(Button, { variant: 'primary', isBusy: saving, disabled: !dirty, onClick: onSave }, dirty ? __('Save changes', 'modern-coming-soon') : __('Saved', 'modern-coming-soon'))
		)
	);
}

function StickySaveBar({ dirty, onSave, saving }) {
	if (!dirty) return null;
	return h('div', { className: 'mcs-savebar' },
		h('div', null, __('You have unsaved changes', 'modern-coming-soon')),
		h(Button, { variant: 'primary', isBusy: saving, onClick: onSave }, __('Save now', 'modern-coming-soon'))
	);
}

function SidebarNav({ tab, onChange }) {
	return h('div', { className: 'mcs-nav' },
		TABS.map((t) =>
			h(Button, { key: t.id, icon: t.icon, isTertiary: tab !== t.id, isPrimary: tab === t.id, onClick: () => onChange(t.id), className: 'mcs-nav__item' }, t.label)
		)
	);
}

function Dashboard({ settings, setSettings }) {
	return h('div', { className: 'mcs-grid' },
		h(Card, null,
			h(CardHeader, null, T('Quick Toggles', 'تنظیمات سریع')),
			h(CardBody, null,
				h('div', { className: 'mcs-toggle-grid' },
					h(ToggleControl, { label: T('RTL layout', 'چیدمان راست‌چین'), checked: settings?.typography?.rtl, onChange: (checked) => setSettings({ ...settings, typography: { ...settings.typography, rtl: checked } }) }),
					h(ToggleControl, { label: T('Countdown', 'شمارش معکوس'), checked: settings?.countdown?.enabled, onChange: (checked) => setSettings({ ...settings, countdown: { ...settings.countdown, enabled: checked } }) }),
					h(ToggleControl, { label: T('Subscription form', 'فرم اشتراک'), checked: settings?.sections?.subscribe, onChange: (checked) => setSettings({ ...settings, sections: { ...settings.sections, subscribe: checked } }) }),
					h(ToggleControl, { label: T('Noindex', 'نوایندکس'), checked: settings?.noindex, onChange: (checked) => setSettings({ ...settings, noindex: checked }) })
				)
			)
		),
		h(Card, null,
			h(CardHeader, null, T('Cache & CDN reminder', 'یادآوری کش و CDN')),
			h(CardBody, null, T('Purge cache/CDN after enabling modes to ensure visitors see the correct page.', 'پس از فعال‌سازی حالت‌ها کش/CDN را پاک کنید تا بازدیدکنندگان صفحه درست را ببینند.'))
		),
		h(Card, null,
			h(CardHeader, null, T('Stats', 'آمار')),
			h(CardBody, null,
				h('div', { className: 'mcs-stats' },
					h('div', null, h('strong', null, (window.mcsAdmin?.subscribers?.total) || 0), h('div', null, T('Subscribers', 'مشترکین'))),
					h('div', null, h('strong', null, settings?.template || 'classic'), h('div', null, T('Template', 'قالب')))
				)
			)
		)
	);
}

function Design({ settings, setSettings, previewUrl }) {
	const update = (patch) => setSettings({ ...settings, ...patch });
	const updateSection = (section, key, value) => setSettings({ ...settings, [section]: { ...(settings?.[section] || {}), [key]: value } });
	const previewRef = useRef(null);
	const [device, setDevice] = useState('desktop');
	const scale = device === 'desktop' ? 1 : device === 'tablet' ? 0.8 : 0.6;

	useEffect(() => {
		const timer = setTimeout(() => {
			if (previewRef.current) previewRef.current.contentWindow.location.reload();
		}, 400);
		return () => clearTimeout(timer);
	}, [settings]);

	const handleLogoSelect = (media) => {
		if (!media) return;
		update({ logo: media.url, logo_id: media.id });
	};

	const openLegacyMedia = () => {
		if (!window.wp?.media) {
			dispatch('core/notices').createErrorNotice(__('Media library not available.', 'modern-coming-soon'));
			return;
		}
		const frame = window.wp.media({
			title: __('Select Logo', 'modern-coming-soon'),
			button: { text: __('Use this', 'modern-coming-soon') },
			multiple: false,
			library: { type: ['image'] },
		});
		frame.on('select', () => {
			const media = frame.state().get('selection').first()?.toJSON?.();
			if (media) handleLogoSelect(media);
		});
		frame.open();
	};

	return h('div', { className: 'mcs-design' },
		h('div', { className: 'mcs-design__controls' },
			h(Card, null,
				h(CardHeader, null, __('Branding', 'modern-coming-soon')),
				h(CardBody, null,
					MediaUpload
						? h(MediaUpload, {
							onSelect: handleLogoSelect,
							allowedTypes: ['image'],
							value: settings?.logo_id || 0,
							render: ({ open }) => h('div', { className: 'mcs-media' },
								h('div', { className: 'mcs-media__preview', onClick: open },
									settings?.logo ? h('img', { src: settings.logo, alt: __('Logo', 'modern-coming-soon') }) : h('div', { className: 'mcs-media__placeholder' }, __('No logo', 'modern-coming-soon'))
								),
								h(Button, { onClick: open, variant: 'secondary' }, __('Select Logo', 'modern-coming-soon')),
								settings?.logo && h(Button, { isDestructive: true, variant: 'link', onClick: () => update({ logo: '', logo_id: 0 }) }, __('Remove', 'modern-coming-soon'))
							),
						})
						: h('div', { className: 'mcs-media' },
							h('div', { className: 'mcs-media__preview', onClick: openLegacyMedia },
								settings?.logo ? h('img', { src: settings.logo, alt: __('Logo', 'modern-coming-soon') }) : h('div', { className: 'mcs-media__placeholder' }, __('No logo', 'modern-coming-soon'))
							),
							h(Button, { onClick: openLegacyMedia, variant: 'secondary' }, __('Select Logo', 'modern-coming-soon')),
							settings?.logo && h(Button, { isDestructive: true, variant: 'link', onClick: () => update({ logo: '', logo_id: 0 }) }, __('Remove', 'modern-coming-soon'))
						),
					h(TextControl, { label: __('Title', 'modern-coming-soon'), value: settings?.title || '', onChange: (val) => update({ title: val }) }),
					h(TextControl, { label: __('Subtitle', 'modern-coming-soon'), value: settings?.subtitle || '', onChange: (val) => update({ subtitle: val }) }),
					h(TextareaControl, { label: __('Description', 'modern-coming-soon'), value: settings?.content || '', onChange: (val) => update({ content: val }) })
				)
			),
			h(Card, null,
				h(CardHeader, null, __('Colors', 'modern-coming-soon')),
				h(CardBody, null,
					h('div', { className: 'mcs-color-row' },
						h('div', null, h('div', null, __('Background', 'modern-coming-soon')),
							h(ColorPicker, { color: settings?.background?.value || '#101010', onChangeComplete: (v) => update({ background: { ...settings.background, value: v.hex } }) })
						),
						h('div', null, h('div', null, __('Button color', 'modern-coming-soon')),
							h(ColorPicker, { color: settings?.button_color || '#0ea5e9', onChangeComplete: (v) => update({ button_color: v.hex }) })
						)
					)
				)
			),
			h(Card, null,
				h(CardHeader, null, __('Typography', 'modern-coming-soon')),
				h(CardBody, null,
					h(TextControl, { label: __('Font family', 'modern-coming-soon'), value: settings?.typography?.font_family || '', onChange: (val) => updateSection('typography', 'font_family', val) }),
					h(ToggleControl, { label: __('RTL', 'modern-coming-soon'), checked: settings?.typography?.rtl, onChange: (checked) => updateSection('typography', 'rtl', checked) }),
					h(RangeControl, { label: __('Title size', 'modern-coming-soon'), value: settings?.title_size || 40, min: 20, max: 72, onChange: (val) => update({ title_size: val }) })
				)
			),
			h(Card, null,
				h(CardHeader, null, __('Sections', 'modern-coming-soon')),
				h(CardBody, null,
					Object.entries(settings?.sections || {}).map(([key, val]) =>
						h(ToggleControl, { key, label: key, checked: !!val, onChange: (checked) => updateSection('sections', key, checked) })
					)
				)
			),
			h(Card, null,
				h(CardHeader, null, __('Buttons', 'modern-coming-soon')),
				h(CardBody, null,
					h(TextControl, { label: __('Label', 'modern-coming-soon'), value: settings?.button_label || '', onChange: (val) => update({ button_label: val }) }),
					h(TextControl, { label: __('URL', 'modern-coming-soon'), value: settings?.button_url || '', onChange: (val) => update({ button_url: val }) })
				)
			),
			h(Card, null,
				h(CardHeader, null, __('Countdown', 'modern-coming-soon')),
				h(CardBody, null,
					h(ToggleControl, { label: __('Enable countdown', 'modern-coming-soon'), checked: settings?.countdown?.enabled, onChange: (checked) => updateSection('countdown', 'enabled', checked) }),
					h(TextControl, { label: __('Target (YYYY-MM-DD HH:MM)', 'modern-coming-soon'), value: settings?.countdown?.date || '', onChange: (val) => updateSection('countdown', 'date', val) })
				)
			)
		),
		h('div', { className: 'mcs-design__preview' },
			h('div', { className: 'mcs-preview-toolbar' },
				h('span', null, __('Live preview', 'modern-coming-soon')),
				h('div', { className: 'mcs-device-toggle' },
					h(Button, { isSmall: true, variant: device === 'desktop' ? 'primary' : 'secondary', onClick: () => setDevice('desktop') }, __('Desktop', 'modern-coming-soon')),
					h(Button, { isSmall: true, variant: device === 'tablet' ? 'primary' : 'secondary', onClick: () => setDevice('tablet') }, __('Tablet', 'modern-coming-soon')),
					h(Button, { isSmall: true, variant: device === 'mobile' ? 'primary' : 'secondary', onClick: () => setDevice('mobile') }, __('Mobile', 'modern-coming-soon'))
				),
				h(Button, { variant: 'secondary', onClick: () => window.open(previewUrl, '_blank') }, __('Open in new tab', 'modern-coming-soon'))
			),
			h('div', { className: 'mcs-preview-shell' },
				h('iframe', { ref: previewRef, title: __('Preview', 'modern-coming-soon'), src: previewUrl, className: 'mcs-preview-frame', style: { transform: `scale(${scale})`, transformOrigin: 'top center', width: `${100 / scale}%`, height: `${700 / scale}px` } })
			)
		)
	);
}

function Templates({ settings, setSettings, pluginUrl, previewUrl }) {
	const [templates, setTemplates] = useState([]);
	const [loading, setLoading] = useState(true);
	const [category, setCategory] = useState('all');
	const [search, setSearch] = useState('');

	useEffect(() => {
		if (templatesCache.size) {
			setTemplates(Array.from(templatesCache.values()));
			setLoading(false);
			return;
		}
		apiFetch({ path: '/modern-coming-soon/v1/templates' })
			.then((resp) => {
				(resp || []).forEach((t) => templatesCache.set(t.slug, t));
				setTemplates(resp || []);
			})
			.finally(() => setLoading(false));
	}, []);

	const filtered = templates.filter((tpl) => {
		if (category !== 'all' && tpl.category !== category) return false;
		if (search && !tpl.name.toLowerCase().includes(search.toLowerCase())) return false;
		return true;
	});
	const categories = ['all', ...new Set(templates.map((t) => t.category))];

	return h('div', null,
		h('div', { className: 'mcs-template-filters' },
			h('div', { className: 'mcs-chips' },
				categories.map((cat) => h(Button, { key: cat, isPrimary: cat === category, isTertiary: cat !== category, onClick: () => setCategory(cat) }, cat))
			),
			h(SearchControl, { value: search, onChange: setSearch, placeholder: __('Search templates', 'modern-coming-soon') })
		),
		loading ? h(Spinner, null) :
			h('div', { className: 'mcs-template-grid' },
				filtered.map((tpl) =>
					h('div', { key: tpl.slug, className: 'mcs-template-card ' + (settings?.template === tpl.slug ? 'is-selected' : ''), onClick: () => setSettings({ ...settings, template: tpl.slug, custom_html: '' }) },
						h('div', { className: 'mcs-template-thumb', style: { backgroundImage: `url(${pluginUrl}${tpl.thumbnail || tpl.screenshot || ''})` } }),
						h('div', { className: 'mcs-template-meta' },
							h('div', { className: 'mcs-template-name' }, tpl.name),
							h('div', { className: 'mcs-template-cat' }, tpl.category)
						),
						h('div', { className: 'mcs-template-actions' },
							h(Button, { variant: 'secondary', onClick: (e) => { e.stopPropagation(); window.open(previewUrl + '&tpl=' + tpl.slug, '_blank'); } }, __('Preview', 'modern-coming-soon')),
							h(Button, { variant: 'primary', onClick: (e) => { e.stopPropagation(); setSettings({ ...settings, template: tpl.slug, custom_html: '' }); } }, __('Use template', 'modern-coming-soon'))
						)
					)
				)
			)
	);
}

function AccessRules({ settings, setSettings }) {
	const update = (patch) => setSettings({ ...settings, ...patch });
	const [tokenMessage, setTokenMessage] = useState('');
	const [showConfirm, setShowConfirm] = useState(false);
	const roles = window.mcsAdmin?.roles || [];
	const ipList = settings?.bypass_ips || [];
	const urlList = settings?.bypass_urls || [];

	const toggleRole = (value) => {
		const existing = new Set(settings?.bypass_roles || []);
		existing.has(value) ? existing.delete(value) : existing.add(value);
		update({ bypass_roles: Array.from(existing) });
	};

	const generateToken = () => {
		apiFetch({ path: '/modern-coming-soon/v1/bypass/regenerate', method: 'POST' })
			.then((res) => setTokenMessage(__('Token generated: ', 'modern-coming-soon') + res.token));
	};

	return h('div', { className: 'mcs-grid' },
		h(Card, null,
			h(CardHeader, null, __('Roles', 'modern-coming-soon')),
			h(CardBody, null,
				h('div', { className: 'mcs-role-list' },
					roles.map((role) =>
						h(CheckboxControl, { key: role.value, label: role.label, checked: (settings?.bypass_roles || []).includes(role.value), onChange: () => toggleRole(role.value) })
					)
				)
			)
		),
		h(Card, null,
			h(CardHeader, null, __('IP allowlist', 'modern-coming-soon')),
			h(CardBody, null,
				h('div', { className: 'mcs-repeater' },
					ipList.map((item, idx) =>
						h('div', { className: 'mcs-repeater__row', key: idx },
							h(TextControl, { label: idx === 0 ? __('IP/CIDR', 'modern-coming-soon') : undefined, value: item, onChange: (val) => { const next = [...ipList]; next[idx] = val; update({ bypass_ips: next }); } }),
							h(Button, { isDestructive: true, onClick: () => update({ bypass_ips: ipList.filter((_, i) => i !== idx) }) }, __('Delete', 'modern-coming-soon'))
						)
					),
					h(Button, { variant: 'secondary', onClick: () => update({ bypass_ips: [...ipList, ''] }) }, __('Add IP', 'modern-coming-soon')),
					h('p', { className: 'description' }, __('Supports single IP or CIDR (e.g., 192.168.0.0/24)', 'modern-coming-soon'))
				)
			)
		),
		h(Card, null,
			h(CardHeader, null, __('URL allowlist', 'modern-coming-soon')),
			h(CardBody, null,
				h('div', { className: 'mcs-repeater' },
					urlList.map((item, idx) =>
						h('div', { className: 'mcs-repeater__row', key: idx },
							h(TextControl, { label: idx === 0 ? __('Path/Regex', 'modern-coming-soon') : undefined, value: item, onChange: (val) => { const next = [...urlList]; next[idx] = val; update({ bypass_urls: next }); } }),
							h(Button, { isDestructive: true, onClick: () => update({ bypass_urls: urlList.filter((_, i) => i !== idx) }) }, __('Delete', 'modern-coming-soon'))
						)
					),
					h(Button, { variant: 'secondary', onClick: () => update({ bypass_urls: [...urlList, ''] }) }, __('Add URL', 'modern-coming-soon')),
					h('p', { className: 'description' }, __('Use relative paths. Regex allowed.', 'modern-coming-soon'))
				)
			)
		),
		h(Card, null,
			h(CardHeader, null, __('Secret token', 'modern-coming-soon')),
			h(CardBody, null,
				h(ToggleControl, { label: __('Enable bypass token', 'modern-coming-soon'), checked: settings?.bypass_token_enabled, onChange: (checked) => update({ bypass_token_enabled: checked }) }),
				h(Button, { variant: 'secondary', onClick: () => setShowConfirm(true) }, __('Generate token', 'modern-coming-soon')),
				tokenMessage && h(Notice, { status: 'info', isDismissible: true },
					h('div', { className: 'mcs-token-row' },
						h('span', null, tokenMessage),
						h(Button, { variant: 'secondary', onClick: () => navigator.clipboard?.writeText(tokenMessage.replace(/^[^:]+:\s*/, '')) }, __('Copy', 'modern-coming-soon'))
					)
				),
				showConfirm && h(Modal, { title: __('Regenerate token?', 'modern-coming-soon'), onRequestClose: () => setShowConfirm(false) },
					h('p', null, __('This will replace the previous token.', 'modern-coming-soon')),
					h(Button, { variant: 'primary', onClick: () => { generateToken(); setShowConfirm(false); } }, __('Confirm', 'modern-coming-soon'))
				)
			)
		),
		h(Card, null,
			h(CardHeader, null, __('Factory mode options', 'modern-coming-soon')),
			h(CardBody, null,
				h(ToggleControl, { label: __('Block REST API', 'modern-coming-soon'), checked: settings?.block_rest_factory, onChange: (checked) => update({ block_rest_factory: checked }) }),
				h(ToggleControl, { label: __('Block XML-RPC', 'modern-coming-soon'), checked: settings?.block_xmlrpc, onChange: (checked) => update({ block_xmlrpc: checked }) })
			)
		)
	);
}

function Subscribers() {
	const [rows, setRows] = useState([]);
	const [search, setSearch] = useState('');
	const [loading, setLoading] = useState(true);
	const [page, setPage] = useState(1);
	const perPage = 20;

	const load = () => {
		setLoading(true);
		apiFetch({ path: `/modern-coming-soon/v1/subscribers?search=${encodeURIComponent(search)}&page=${page}&per_page=${perPage}` })
			.then((resp) => setRows(resp.items || []))
			.finally(() => setLoading(false));
	};

	useEffect(load, [search, page]);

	return h('div', null,
		h('div', { className: 'mcs-subs-toolbar' },
			h(SearchControl, { value: search, onChange: setSearch, placeholder: __('Search emails', 'modern-coming-soon') }),
			h(Button, { variant: 'secondary', onClick: () => {
				apiFetch({ path: '/modern-coming-soon/v1/subscribers/export' }).then((res) => {
					const blob = atob(res.csv);
					const url = URL.createObjectURL(new Blob([blob], { type: 'text/csv' }));
					const a = document.createElement('a');
					a.href = url;
					a.download = 'subscribers.csv';
					a.click();
				});
			} }, __('Export CSV', 'modern-coming-soon'))
		),
		loading ? h(Spinner, null) :
			h('table', { className: 'widefat fixed striped' },
				h('thead', null, h('tr', null,
					h('th', null, __('Email', 'modern-coming-soon')),
					h('th', null, __('Date', 'modern-coming-soon')),
					h('th', null, __('Source', 'modern-coming-soon'))
				)),
				h('tbody', null,
					rows.map((row) => h('tr', { key: row.id },
						h('td', null, row.email),
						h('td', null, row.created_at),
						h('td', null, row.source)
					))
				)
			)
	);
}

function Advanced({ settings, setSettings }) {
	const update = (patch) => setSettings({ ...settings, ...patch });
	return h('div', { className: 'mcs-grid' },
		h(Card, null,
			h(CardHeader, null, __('Status codes', 'modern-coming-soon')),
			h(CardBody, null,
				h(TextControl, { label: __('Coming Soon status', 'modern-coming-soon'), value: settings?.status_code || 200, onChange: (val) => update({ status_code: parseInt(val, 10) || 200 }) }),
				h(TextControl, { label: __('Factory status', 'modern-coming-soon'), value: settings?.status_code_factory || 503, onChange: (val) => update({ status_code_factory: parseInt(val, 10) || 503 }) }),
				h(TextControl, { label: __('Retry-After (seconds)', 'modern-coming-soon'), value: settings?.retry_after || 0, onChange: (val) => update({ retry_after: parseInt(val, 10) || 0 }) })
			)
		),
		h(Card, null,
			h(CardHeader, null, __('SEO', 'modern-coming-soon')),
			h(CardBody, null,
				h(ToggleControl, { label: __('Noindex', 'modern-coming-soon'), checked: settings?.noindex, onChange: (checked) => update({ noindex: checked }) }),
				h(TextareaControl, { label: __('Meta description', 'modern-coming-soon'), value: settings?.seo_description || '', onChange: (val) => update({ seo_description: val }) })
			)
		),
		h(Card, null,
			h(CardHeader, null, __('Notes', 'modern-coming-soon')),
			h(CardBody, null, __('Clear cache/CDN after toggling modes. Keep wp-admin open while testing Factory mode.', 'modern-coming-soon'))
		)
	);
}

function App() {
	const { settings, setSettings, loading, dirty, save, saving } = useSettings();
	const [tab, setTab] = useState('dashboard');
	const data = window.mcsAdmin || {};
	const previewUrlWithTemplate = data.previewUrl + '&tpl=' + encodeURIComponent(settings?.template || '');

	if (loading || !settings) return h('div', { className: 'mcs-loading' }, h(Spinner, null));

	let main = null;
	switch (tab) {
		case 'dashboard': main = h(Dashboard, { settings, setSettings }); break;
		case 'design': main = h(Design, { settings, setSettings, previewUrl: previewUrlWithTemplate }); break;
		case 'templates': main = h(Templates, { settings, setSettings, pluginUrl: data.pluginUrl, previewUrl: data.previewUrl }); break;
		case 'access': main = h(AccessRules, { settings, setSettings }); break;
		case 'subscribers': main = h(Subscribers, null); break;
		case 'advanced': main = h(Advanced, { settings, setSettings }); break;
		default: main = null;
	}

	return h('div', { className: 'mcs-admin-app ' + (data.isRTL ? 'is-rtl' : '') },
		h(HeaderBar, { mode: settings.mode, onModeChange: (mode) => setSettings({ ...settings, mode }), dirty, onSave: save, saving, previewUrl: previewUrlWithTemplate }),
		h('div', { className: 'mcs-body' },
			h(SidebarNav, { tab, onChange: setTab }),
			h('div', { className: 'mcs-main' }, main)
		),
		h(StickySaveBar, { dirty, onSave: save, saving })
	);
}

domReady(() => {
	const root = document.getElementById('modern-coming-soon-admin');
	if (!root) return;
	apiFetch.use(apiFetch.createNonceMiddleware(window.mcsAdmin?.nonce));
	wp.element.render(h(App), root);
});
