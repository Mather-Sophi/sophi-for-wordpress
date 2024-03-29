/**
 * Sophi post item block
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { listItem as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import block from './block.json';

// CSS file.
import './index.css';

/**
 * Register block
 */
registerBlockType(block.name, {
	title: __('Sophi Recommended Post', 'sophi-wp'),
	description: __(
		'This is the recommended post from Sophi Site Automation for the respective page and widget combination for the parent Site Automation block.',
		'sophi-wp',
	),
	parent: ['sophi/site-automation-block'],
	edit,
	save,
	icon: { src: icon },
});
