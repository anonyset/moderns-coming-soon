(function (wp) {
	const { __ } = wp.i18n;
	const { registerBlockType } = wp.blocks;
	const { TextControl } = wp.components;

	registerBlockType('modern-coming-soon/countdown', {
		title: __('Coming Soon Countdown', 'modern-coming-soon'),
		icon: 'clock',
		category: 'widgets',
		attributes: {
			date: { type: 'string', default: '' },
		},
		edit: function ({ attributes, setAttributes }) {
			return wp.element.createElement(TextControl, {
				label: __('Date (YYYY-MM-DD HH:MM)', 'modern-coming-soon'),
				value: attributes.date,
				onChange: (date) => setAttributes({ date }),
				help: __('Rendered on the frontend.', 'modern-coming-soon'),
			});
		},
		save: () => null,
	});
})(window.wp);
