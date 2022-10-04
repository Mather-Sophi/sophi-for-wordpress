/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	InnerBlocks,
	useBlockProps,
	store as blockEditorStore,
	getBlocks,
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
 * @param {object}   props                                   The block props.
 * @param {object}   props.attributes                        Block attributes.
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
 * @returns {Function} Render the edit screen
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
	const { replaceInnerBlocks } = dispatch(blockEditorStore);
	const ALLOWED_BLOCKS = ['sophi/page-list-item'];

	// Subscribe to the innerBlocks' state changing.
	const innerBlocks = useSelect((select) => {
		return select(blockEditorStore).getBlock(clientId).innerBlocks;
	});

	const sophiEndpoint = '/sophi/v1/';

	const getPosts = async () => {
		if (pageName === '' || widgetName === '') {
			return;
		}

		const queryArgs = {
			pageName,
			widgetName,
			displayFeaturedImage,
		};

		const updatedInnerBlocks = [];

		await apiFetch({
			path: addQueryArgs(`${sophiEndpoint}get-posts`, {
				...queryArgs,
			}),
			method: 'GET',
		}).then(
			(data) => {
				// eslint-disable-next-line array-callback-return
				data.map((item) => {
					updatedInnerBlocks.push(
						createBlock('sophi/page-list-item', {
							postTitle: item.post_title,
							postUpdated: false,
							postExcept: displayPostExcept ? item.post_excerpt : '',
							featuredImage: displayFeaturedImage ? item.featuredImage : '',
							linkToFeaturedImage: addLinkToFeaturedImage,
							postAuthor: displayAuthor ? item.postAuthor : '',
							postDate: displayPostDate ? item.postDate : '',
							postDateC: displayPostDate ? item.postDateC : '',
						}),
					);
				});
			},
			(err) => {
				console.log(err);
			},
		);

		// Replace innerBlocks with the updated array.
		replaceInnerBlocks(clientId, updatedInnerBlocks, false);
	};

	const updatePost = async ({ ruleType, postID, overrideExpiry, position }) => {
		const queryArgs = {
			ruleType,
			postID,
			overrideExpiry,
			position,
			pageName,
			widgetName,
		};

		const updatedInnerBlocks = [];

		await apiFetch({
			path: addQueryArgs(`${sophiEndpoint}update-posts`, {
				...queryArgs,
			}),
			method: 'POST',
		}).then(
			(data) => {
				console.log(data);
			},
			(err) => {
				console.log(err);
			},
		);

		// Replace innerBlocks with the updated array.
		replaceInnerBlocks(clientId, updatedInnerBlocks, false);
	};

	if (undefined !== innerBlocks) {
		// && undefined === innerBlocks[2]

		let updatesRequired = false;
		const updatesInnerBlocks = [];
		// eslint-disable-next-line array-callback-return
		innerBlocks.map((item, index) => {
			if (item.attributes.postUpdated) {
				innerBlocks[index].attributes.postUpdated = false;

				if (item.attributes.overrideRule === 'add') {
					innerBlocks[index].attributes.overrideRule = '';

					const { overridePostID, overrideExpiry } = item.attributes;
					const postData = wp.data
						.select('core')
						.getEntityRecord('postType', 'post', overridePostID);

					// make an API call and create new/updated block.
					const newInnerBlocks = createBlock('sophi/page-list-item', {
						postTitle: postData.title.raw,
					});

					updatesInnerBlocks.push(newInnerBlocks);
					updatesRequired = true;

					// Update the post at API level.
					updatePost({
						ruleType: 'in',
						overridePostID,
						overrideExpiry,
						position: index + 1,
					});
				}
			}

			updatesInnerBlocks.push(item);
		});

		if (updatesRequired) {
			replaceInnerBlocks(clientId, updatesInnerBlocks, false);
		}
	}

	useEffect(() => {
		getPosts();
	}, []);

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
				{...blockProps}
			>
				<InnerBlocks
					allowedBlocks={ALLOWED_BLOCKS}
					templateLock="all"
					template={['sophi/page-list-item']}
				/>
			</div>
		</div>
	);
};
// Set the propTypes
SiteAutomationBlockEdit.propTypes = editPropsShape;

export default SiteAutomationBlockEdit;
