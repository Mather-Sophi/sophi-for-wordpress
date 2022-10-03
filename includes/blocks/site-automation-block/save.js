import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
 *
 * @return {null} Dynamic blocks do not save the HTML.
 */
const SiteAutomationBlockSave = () => {
	return (
		<div { ...useBlockProps.save() }>
			<div>
				<InnerBlocks.Content />
			</div>
		</div>
	)
};

export default SiteAutomationBlockSave;
