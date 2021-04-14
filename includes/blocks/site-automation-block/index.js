/**
 * Example-block
 * Custom title block -- feel free to delete
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
import { ReactComponent as icon } from './icon.svg';

/* Uncomment for CSS overrides in the admin */
// import './index.css';

/**
 * Register block
 */
registerBlockType(block.name, {
	title: __('Site Automation', 'sophi-wp'),
	description: __('Display curated content by Sophi', 'sophi-wp'),
	edit,
	save,
	icon,
});
