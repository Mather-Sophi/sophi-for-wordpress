/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	InnerBlocks,
	useBlockProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import apiFetch from '@wordpress/api-fetch';
import { PanelBody, TextControl, ToggleControl, Placeholder } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { useSelect, dispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';
import { useDebounce } from '@wordpress/compose';

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
 * @param {string}   props.clientId                          Block's client ID.
 * @param {Function} props.setAttributes                     Sets the value for block attributes.
 *
 * @returns {Function} Render the edit screen
 */
const SiteAutomationBlockEdit = ({
	attributes: {
		pageName = '',
		widgetName = '',
		displayPostExcept = false,
		displayAuthor = false,
		displayPostDate = false,
		displayFeaturedImage = false,
		addLinkToFeaturedImage = false,
	},
	className,
	setAttributes,
	clientId,
}) => {
	const blockProps = useBlockProps();
	const [postsFound, setPostsFound] = useState(false);
	const [message, setMessage] = useState({
		text: '',
		color: 'green',
	});
	const { replaceInnerBlocks } = dispatch(blockEditorStore);
	const ALLOWED_BLOCKS = ['sophi/page-list-item'];

	// Subscribe to the innerBlocks' state changing.
	const innerBlocks = useSelect((select) => {
		return select(blockEditorStore).getBlock(clientId).innerBlocks;
	});

	const sophiEndpoint = '/sophi/v1/';

	const curatorPosts = async (overridePostID = '') => {
		if (pageName === '' || widgetName === '') {
			return;
		}

		const queryArgs = {
			overridePostID,
			pageName,
			widgetName,
			displayFeaturedImage,
			displayAuthor,
			displayPostDate,
			displayPostExcept,
		};

		const notFoundPosts = [];
		const updatedInnerBlocks = [];

		await apiFetch({
			path: addQueryArgs(`${sophiEndpoint}site-automation`, {
				...queryArgs,
			}),
			method: 'GET',
		}).then(
			// eslint-disable-next-line consistent-return
			(data) => {
				// eslint-disable-next-line array-callback-return
				data.forEach((item) => {
					// If item is integer, it is a Post ID, that does not exist in the site.
					if (Number.isInteger(item)) {
						notFoundPosts.push(item);
						return;
					}

					updatedInnerBlocks.push(
						createBlock('sophi/page-list-item', {
							postUpdated: false,
							postID: item.ID,
							postTitle: item.post_title,
							postLink: item.postLink,
							postExcept: displayPostExcept ? item.post_excerpt : '',
							featuredImage:
								displayFeaturedImage && item.featuredImage
									? item.featuredImage
									: '',
							linkToFeaturedImage: addLinkToFeaturedImage,
							postAuthor: displayAuthor ? item.postAuthor : '',
							postDate: displayPostDate ? item.postDate : '',
							postDateC: displayPostDate ? item.postDateC : '',
						}),
					);
				});
			},
			(err) => {
				setMessage({
					text: err.message,
					color: 'red',
				});
			},
		);

		if (notFoundPosts.length) {
			setMessage({
				text:
					__(
						`Please contact your Sophi.io account representative as the Sophi service is providing post IDs that are not valid on this site:`,
						'sophi-wp',
					) + notFoundPosts.join(', '),
				color: 'red',
			});
		}

		if (overridePostID !== '') {
			// eslint-disable-next-line consistent-return
			return updatedInnerBlocks;
		}

		if (updatedInnerBlocks.length) {
			// Replace innerBlocks with the updated array.
			setPostsFound(true);
			replaceInnerBlocks(clientId, updatedInnerBlocks, false);
		} else {
			setPostsFound(false);
		}
	};

	const overridePost = async ({ ruleType, overridePostID, overrideExpiry, position }) => {
		const queryArgs = {
			ruleType,
			overridePostID,
			overrideExpiry,
			position,
			pageName,
			widgetName,
		};

		await apiFetch({
			path: addQueryArgs(`${sophiEndpoint}site-automation-override`, {
				...queryArgs,
			}),
			method: 'POST',
		}).then(
			() => {
				setMessage({
					text: __(`The "${ruleType}" override request sent to Sophi.`),
					color: 'green',
				});
			},
			(err) => {
				setMessage({
					text: err.message,
					color: 'red',
				});
			},
		);
	};

	// eslint-disable-next-line consistent-return
	const updateInnerBlocks = async () => {
		if (undefined === innerBlocks) {
			return false;
		}

		const index = innerBlocks.findIndex((item) => item.attributes.postUpdated === true);
		if (index === -1) {
			return false;
		}

		let { overridePostID } = innerBlocks[index].attributes;

		// eslint-disable-next-line no-use-before-define
		const { postID, overrideRule, overrideExpiry } = innerBlocks[index].attributes;

		// Reset override attributes before render new set.
		innerBlocks[index].attributes.postUpdated = false;
		innerBlocks[index].attributes.overrideRule = '';
		innerBlocks[index].attributes.overridePostID = 0;
		innerBlocks[index].attributes.overrideLocation = '';

		// Update the inner blocks.
		// eslint-disable-next-line no-await-in-loop
		const removeItems = overrideRule === 'in' ? 0 : 1;
		if (overridePostID !== 0 && overridePostID !== undefined) {
			// When add/replace.
			const newInnerBlocks = await curatorPosts(overridePostID);
			innerBlocks.splice(index, removeItems, newInnerBlocks[0]);
		}

		// Remove the last item after adding the new item.
		if (overrideRule === 'in') {
			innerBlocks.pop();
		}

		// Replace now.
		replaceInnerBlocks(clientId, innerBlocks, false);

		// Set selected post ID as an override ID when remove/ban.
		if (overrideRule === 'out' || overrideRule === 'ban') {
			overridePostID = postID;
		}

		// Update the post at API level.
		overridePost({
			ruleType: overrideRule,
			overridePostID,
			overrideExpiry,
			position: index + 1,
		});
	};
	updateInnerBlocks();

	useEffect(() => {
		curatorPosts();
		// eslint-disable-next-line
	}, [
		displayFeaturedImage,
		displayAuthor,
		displayPostDate,
		displayPostExcept,
		addLinkToFeaturedImage,
	]);

	// Delay the API call to prevent unnecessary calls on typing each character.
	const debouncedFetchData = useDebounce(curatorPosts, 1000);
	useEffect(() => {
		debouncedFetchData();
		// eslint-disable-next-line
	}, [
		pageName,
		widgetName,
	]);

	return (
		<div className={className}>
			<InspectorControls>
				{message.text && (
					<div
						style={{
							color: message.color,
							border: '1px solid',
							padding: '10px',
							margin: '10px',
						}}
					>
						{message.text}
					</div>
				)}
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

			{!(pageName && widgetName && postsFound) && (
				<Placeholder label={__('Sophi.io Site Automation', 'sophi-wp')}>
					<p>
						{__(
							'Please set page and widget name for this Site Automation block on the sidebar settings.',
							'sophi-wp',
						)}
					</p>
				</Placeholder>
			)}

			{pageName && widgetName && postsFound && (
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
			)}
		</div>
	);
};
// Set the propTypes
SiteAutomationBlockEdit.propTypes = editPropsShape;

export default SiteAutomationBlockEdit;
