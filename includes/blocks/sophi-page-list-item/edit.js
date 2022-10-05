/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Popover, ToolbarButton, ToolbarGroup, SelectControl } from '@wordpress/components';
import {
	BlockControls,
	useBlockProps,
} from '@wordpress/block-editor';
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
 * @param {object}   props                                   The block props.
 * @param {object}   props.attributes                        Block attributes.
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
		postUpdated = false,
		overrideRule = '',
		overridePostID = 0,
		overrideExpiry = 2,
	},
	className,
	setAttributes,
}) => {
	const blockProps = useBlockProps();
	const [showPopup, setShowPopup] = useState(false);
	const [selectedPost, setSelectedPost] = useState();

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
		console.log(overrideExpiry);
		if (overrideRule === '') {
			alert('Please select override rule');
			return;
		}
		if (overridePostID === 0) {
			alert('Please select the post');
			return;
		}
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
				<div className="override-row">
					<ContentPicker
						content={selectedPost}
						onPickChange={handlePickChanage}
						mode="post"
						label="Please select a Post:"
						contentTypes={['post']}
					/>
				</div>
				<div className="override-row">
					{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
					<label>Expire override on:</label>{' '}
					<input
						type="number"
						style={{ maxWidth: '40px' }}
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

	return (
		<div className={className}>
			<BlockControls group="other">
				<ToolbarGroup>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="admin-generic"
						isPressed={showPopup}
						label="Override"
						onClick={onToggle}
					/>
				</ToolbarGroup>
			</BlockControls>
			{overridePopover}
			<div className="curated-item" {...blockProps}>
				{featuredImage && linkToFeaturedImage && (
					// eslint-disable-next-line jsx-a11y/anchor-is-valid
					<Link value={featuredImage} url={postLink} />
				)}
				{featuredImage && !linkToFeaturedImage && { featuredImage }}
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
