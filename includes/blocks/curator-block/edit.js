/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, Placeholder } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { editPropsShape } from './props-shape';

/**
 * Edit component.
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   props                        The block props.
 * @param {Object}   props.attributes             Block attributes.
 * @param {string}   props.attributes.pageName    Page name for curator request.
 * @param {string}   props.attributes.widgetName  Widget name for curator request.
 * @param {string}   props.className              Class name for the block.
 * @param {Function} props.setAttributes          Sets the value for block attributes.
 * @return {Function} Render the edit screen
 */
const CuratorBlockEdit = ({
	attributes: {
		pageName,
		widgetName,
		displayPostExcept,
		displayAuthor,
		displayPostDate,
		displayFeaturedImage,
		addLinkToFeaturedImage,
	},
	className,
	setAttributes,
}) => {
	return (
		<div className={className}>
			<InspectorControls>
				<PanelBody title={__('Curator settings', 'sophi-wp')}>
					<TextControl
						label={__('Page name', 'sophi-wp')}
						value={pageName}
						onChange={(value) => setAttributes({ pageName: value })}
					/>
					<TextControl
						label={__('Widget name', 'sophi-wp')}
						value={widgetName}
						onChange={(value) => setAttributes({ widgetName: value })}
					/>
				</PanelBody>
				<PanelBody title={__('Post content settings', 'sophi-wp')}>
					<ToggleControl
						label={__('Post excerpt', 'sophi-wp')}
						checked={displayPostExcept}
						onChange={(value) => setAttributes({ displayPostExcept: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Post meta settings')}>
					<ToggleControl
						label={__('Display author name')}
						checked={displayAuthor}
						onChange={(value) => setAttributes({ displayAuthor: value })}
					/>
					<ToggleControl
						label={__('Display post date')}
						checked={displayPostDate}
						onChange={(value) => setAttributes({ displayPostDate: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Featured image settings')}>
					<ToggleControl
						label={__('Display featured image')}
						checked={displayFeaturedImage}
						onChange={(value) => setAttributes({ displayFeaturedImage: value })}
					/>
					{displayFeaturedImage && (
						<ToggleControl
							label={__('Add link to featured image')}
							checked={addLinkToFeaturedImage}
							onChange={(value) =>
								setAttributes({
									addLinkToFeaturedImage: value,
								})
							}
						/>
					)}
				</PanelBody>
			</InspectorControls>

			{!(pageName && widgetName) && (
				<Placeholder label={__('Sophi Curator')}>
					<p>
						Please set page and widget name for this curator block on the sidebar
						settings.
					</p>
				</Placeholder>
			)}
			{pageName && widgetName && <div className="post-list">Post list goes here.</div>}
		</div>
	);
};
// Set the propTypes
CuratorBlockEdit.propTypes = editPropsShape;

export default CuratorBlockEdit;
