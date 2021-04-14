/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

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
 * @param {string}   props.attributes.pageName    Page name for Site Automation request.
 * @param {string}   props.attributes.widgetName  Widget name for Site Automation request.
 * @param {string}   props.className              Class name for the block.
 * @param {Function} props.setAttributes          Sets the value for block attributes.
 * @return {Function} Render the edit screen
 */
const SiteAutomationBlockEdit = ({
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
				<PanelBody title={__('Site Automation settings', 'sophi-wp')}>
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

				<PanelBody title={__('Post meta settings', 'sophi-wp')}>
					<ToggleControl
						label={__('Display author name', 'sophi-wp')}
						checked={displayAuthor}
						onChange={(value) => setAttributes({ displayAuthor: value })}
					/>
					<ToggleControl
						label={__('Display post date', 'sophi-wp')}
						checked={displayPostDate}
						onChange={(value) => setAttributes({ displayPostDate: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Featured image settings', 'sophi-wp')}>
					<ToggleControl
						label={__('Display featured image', 'sophi-wp')}
						checked={displayFeaturedImage}
						onChange={(value) => setAttributes({ displayFeaturedImage: value })}
					/>
					{displayFeaturedImage && (
						<ToggleControl
							label={__('Add link to featured image', 'sophi-wp')}
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
				<Placeholder label={__('Sophi.io Site Automation', 'sophi-wp')}>
					<p>
						{__(
							'Please set page and widget name for this Site Automation block on the sidebar settings.',
							'sophi-wp',
						)}
					</p>
				</Placeholder>
			)}
			{pageName && widgetName && (
				<ServerSideRender
					block="sophi/site-automation-block"
					attributes={{
						pageName,
						widgetName,
						displayPostExcept,
						displayAuthor,
						displayPostDate,
						displayFeaturedImage,
						addLinkToFeaturedImage,
					}}
				/>
			)}
		</div>
	);
};
// Set the propTypes
SiteAutomationBlockEdit.propTypes = editPropsShape;

export default SiteAutomationBlockEdit;
