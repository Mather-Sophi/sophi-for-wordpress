/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	InnerBlocks,
	useBlockProps,
	store as blockEditorStore,
	getBlocks
} from '@wordpress/block-editor';
import apiFetch from '@wordpress/api-fetch';
import { PanelBody, TextControl, ToggleControl, Placeholder } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { useSelect, dispatch, useRegistry, useDispatch, select } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { editPropsShape } from './props-shape';

/**
 * Edit component.
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   props                                   The block props.
 * @param {Object}   props.attributes                        Block attributes.
 * @param {string}   props.attributes.pageName               Page name for Site Automation request.
 * @param {string}   props.attributes.widgetName             Widget name for Site Automation request.
 * @param {boolean}  props.attributes.displayPostExcept      Whether to display post excerpt.
 * @param {boolean}  props.attributes.displayAuthor          Whether to display post author.
 * @param {boolean}  props.attributes.displayPostDate        Whether to display post date.
 * @param {boolean}  props.attributes.displayFeaturedImage   Whether to display featured image.
 * @param {boolean}  props.attributes.addLinkToFeaturedImage Whether to add post permalink to featured image.
 * @param {string}   props.className                         Class name for the block.
 * @param {Function} props.setAttributes                     Sets the value for block attributes.
 *
 * @return {Function} Render the edit screen
 */
const SiteAutomationBlockEdit = ({
	attributes: {
		pageName = '',
		widgetName = '',
		displayPostExcept,
		displayAuthor,
		displayPostDate,
		displayFeaturedImage,
		addLinkToFeaturedImage,
	},
	className,
	setAttributes,
	clientId,
}) => {
	const blockProps = useBlockProps();
	const {replaceInnerBlocks} = dispatch(blockEditorStore);
	const ALLOWED_BLOCKS = ['sophi/page-list-item'];
	const sophiEndpoint = '/sophi/v1/';

	const getPosts = async () => {

		if( '' === pageName || '' === widgetName ) {
			return;
		}

		const queryArgs = {
			pageName,
			widgetName
		};

		let updatedInnerBlocks = [];

		await apiFetch({
			path: addQueryArgs(`${sophiEndpoint}get-posts`, {
				...queryArgs,
			}),
			method: 'GET',
		}).then(
			(data) => {
				data.map((item, index) => {
					updatedInnerBlocks.push(
						createBlock(
							'sophi/page-list-item', {
								postTitle: item.post_title,
								postUpdated: false,
							},
						)
					)
				});
			},
			(err) => {
				console.log(err);
			}
		);

		// Replace innerBlocks with the updated array.
		replaceInnerBlocks(clientId, updatedInnerBlocks, false);
	}

	useEffect( () => {
		getPosts();
	}, [] );

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

			<div
				className="sophi-site-automation-block"
				id={`sophi-${pageName}-${widgetName}`}
				data-sophi-feature={widgetName}
				{ ...blockProps }
			>
				<InnerBlocks
					allowedBlocks={ ALLOWED_BLOCKS }
					templateLock='all'
					template={ [ 'sophi/page-list-item' ] }
				/>
			</div>
		</div>
	);
};
// Set the propTypes
SiteAutomationBlockEdit.propTypes = editPropsShape;

export default SiteAutomationBlockEdit;
