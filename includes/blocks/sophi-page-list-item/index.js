/**
 * Sophi post item block
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

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
	title: __('Sophi Post Item', 'sophi-wp'),
	description: __('Display curated content by Sophi', 'sophi-wp'),
	parent: ['sophi/site-automation-block'],
	edit,
	save,
	icon: { src: 'admin-post' },
});
