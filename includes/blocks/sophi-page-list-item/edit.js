/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Popover, ToolbarButton, ToolbarGroup, SelectControl } from '@wordpress/components';
import { BlockControls, useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { Link, ContentPicker } from '@10up/block-components';

/**
 * Internal dependencies
 */
import { editPropsShape } from './props-shape';

/**
 * Edit component.
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
 *
 * @param {object}   props                                  The block props.
 * @param {object}   props.attributes                       Block attributes.
 * @param {boolean}  props.attributes.postTitle 			Post Title.
 * @param {boolean}  props.attributes.postLink          	Whether to display post author.
 * @param {boolean}  props.attributes.postExcept        	Whether to display post date.
 * @param {boolean}  props.attributes.postAuthor   			Whether to display featured image.
 * @param {boolean}  props.attributes.postDate 				Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.postDateC 			Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.featuredImage 		Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.linkToFeaturedImage 	Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overrideRule 			Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overridePostID 		Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overrideExpiry 		Whether to add post permalink to featured image.
 * @param {string}   props.className                        Class name for the block.
 * @param {Function} props.setAttributes                    Sets the value for block attributes.
 *
 * @returns {Function} Render the edit screen
 */
const SiteAutomationItemBlockEdit = ({
	attributes: {
		postTitle = '',
		postLink = '',
		postExcept = '',
		postAuthor = '',
		postDate = '',
		postDateC = '',
		featuredImage = '',
		linkToFeaturedImage = false,
		overrideRule = '',
		overridePostID = 0,
		overrideExpiry = 2,
	},
	className,
	setAttributes,
}) => {
	const blockProps = useBlockProps();
	const [showPopup, setShowPopup] = useState(false);
	const [selectedPost, setSelectedPost] = useState('');

	const onToggle = () => {
		setShowPopup((showPopup) => !showPopup);
	};

	const handlePickChanage = (pickedContent) => {
		setAttributes({
			overridePostID: pickedContent.length === 0 ? 0 : pickedContent[0].id,
		});
		setSelectedPost(pickedContent);
	};

	const handleOverride = () => {
		if (overrideRule === '') {
			// eslint-disable-next-line
			alert('Please select override rule');
			return;
		}
		if (overridePostID === 0 && (overrideRule === 'in' || overrideRule === 'replace')) {
			// eslint-disable-next-line
			alert('Please select the post');
			return;
		}

		// Close the popup.
		setShowPopup(false);

		// Set postUpdated to true so this get caught by the parent.
		setAttributes({
			overrideExpiry,
			postUpdated: true,
		});
	};

	const overridePopover = showPopup && (
		<Popover
			className="override-map-popover"
			position="bottom center"
			focusOnMount="firstElement"
			key="override-popover"
			expandOnMobile
			headerTitle={__('Override', 'Override')}
			onFocusOutside={() => setShowPopup(false)}
		>
			<div className="override-popup">
				<div className="override-row">
					<SelectControl
						label=""
						value={overrideRule}
						options={[
							{ label: 'Select an action', value: '' },
							{ label: 'Add a post here', value: 'in' },
							{ label: 'Replace this post', value: 'replace' },
							{ label: 'Remove this post', value: 'out' },
							{ label: 'Ban this post', value: 'ban' },
						]}
						onChange={(newRule) => setAttributes({ overrideRule: newRule })}
						__nextHasNoMarginBottom
					/>
				</div>
				{(overrideRule === 'in' || overrideRule === 'replace') && (
					<div className="override-row">
						<ContentPicker
							content={selectedPost}
							onPickChange={handlePickChanage}
							mode="post"
							label="Please select a post:"
							contentTypes={['post']}
						/>
					</div>
				)}
				<div className="override-row">
					{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
					<label htmlFor="override_expiry">Expire override in:</label>{' '}
					<input
						type="number"
						style={{ maxWidth: '40px' }}
						id="override_expiry"
						value={overrideExpiry}
						onChange={(newExpiry) =>
							setAttributes({ overrideExpiry: newExpiry.target.value })
						}
					/>{' '}
					<span>hours</span>
				</div>
				<input
					type="submit"
					value="Submit Override"
					className="button-primary"
					onClick={handleOverride}
				/>
			</div>
		</Popover>
	);

	const featuredImageTag = (
		// eslint-disable-next-line react/no-danger
		<div style={{ maxWidth: '250px' }} dangerouslySetInnerHTML={{ __html: featuredImage }} /> // phpcs:ignore
	);

	return (
		<div className={className}>
			<BlockControls group="other">
				<ToolbarGroup>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="admin-generic"
						label="Override"
						onClick={onToggle}
					/>
				</ToolbarGroup>
			</BlockControls>
			{overridePopover}
			<div className="curated-item" {...blockProps}>
				{featuredImage && !linkToFeaturedImage && featuredImageTag}
				{featuredImage && linkToFeaturedImage && <a href={postLink}>{featuredImageTag}</a>}
				{/* eslint-disable-next-line jsx-a11y/anchor-is-valid */}
				<Link value={postTitle} url={postLink} />
				{postAuthor && <div className="post-author">{postAuthor}</div>}
				<time dateTime={postDateC} className="post-date">
					{postDate}
				</time>
				<p className="post-excerpt">{postExcept}</p>
			</div>
		</div>
	);
};
// Set the propTypes
SiteAutomationItemBlockEdit.propTypes = editPropsShape;

export default SiteAutomationItemBlockEdit;
