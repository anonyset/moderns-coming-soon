(function (wp) {
	const { __ } = wp.i18n;
	const { registerBlockType } = wp.blocks;
	const { TextControl } = wp.components;

	registerBlockType('modern-coming-soon/subscription', {
		title: __('Coming Soon Subscription', 'modern-coming-soon'),
		icon: 'email',
		category: 'widgets',
		attributes: {
			title: { type: 'string', default: __('Stay in the loop', 'modern-coming-soon') },
			subtitle: { type: 'string', default: __('Get notified when we launch.', 'modern-coming-soon') },
		},
		edit: function ({ attributes, setAttributes }) {
			return wp.element.createElement(
				'div',
				{ className: 'mcs-subscribe-block' },
				wp.element.createElement(TextControl, {
					label: __('Title', 'modern-coming-soon'),
					value: attributes.title,
					onChange: (title) => setAttributes({ title }),
				}),
				wp.element.createElement(TextControl, {
					label: __('Subtitle', 'modern-coming-soon'),
					value: attributes.subtitle,
					onChange: (subtitle) => setAttributes({ subtitle }),
				})
			);
		},
		save: () => null,
	});
})(window.wp);
