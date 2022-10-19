/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Popover, ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { BlockControls, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useState, useRef } from '@wordpress/element';
import { Link, ContentPicker } from '@10up/block-components';

/**
 * Internal dependencies
 */
import { editPropsShape } from './props-shape';

/**
 * Edit component.
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
 *
 * @param {object}   props                                The block props.
 * @param {object}   props.attributes                     Block attributes.
 * @param {boolean}  props.attributes.postTitle           Post Title.
 * @param {boolean}  props.attributes.postLink            Whether to display post author.
 * @param {boolean}  props.attributes.postExcept          Whether to display post date.
 * @param {boolean}  props.attributes.postAuthor          Whether to display featured image.
 * @param {boolean}  props.attributes.postDate            Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.postDateC           Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.featuredImage       Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.linkToFeaturedImage Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overrideRule        Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overridePostID      Whether to add post permalink to featured image.
 * @param {boolean}  props.attributes.overrideExpiry      Whether to add post permalink to featured image.
 * @param {string}   props.className                      Class name for the block.
 * @param {Function} props.setAttributes                  Sets the value for block attributes.
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
	const toolbarButtonRef = useRef();
	const [showPopup, setShowPopup] = useState(false);
	const [selectedPost, setSelectedPost] = useState('');
	const [message, setMessage] = useState({
		text: '',
		color: 'green',
	});

	const onAdd = () => {
		setAttributes({ overrideRule: 'in' });
		setShowPopup((showPopup) => !showPopup);
	};

	const onReplace = () => {
		setAttributes({ overrideRule: 'replace' });
		setShowPopup((showPopup) => !showPopup);
	};

	const onRemove = () => {
		setAttributes({ overrideRule: 'out' });
		setShowPopup((showPopup) => !showPopup);
	};

	const onBan = () => {
		setAttributes({ overrideRule: 'ban' });
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
			setMessage({
				text: __('Please select override rule.', 'sophi-wp'),
				color: 'red',
			});
			return;
		}
		if (overridePostID === 0 && (overrideRule === 'in' || overrideRule === 'replace')) {
			setMessage({
				text: __('Please select the post', 'sophi-wp'),
				color: 'red',
			});
			return;
		}

		// Close the popup & reset the selected post.
		setShowPopup(false);
		setSelectedPost('');

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
			anchorRef={toolbarButtonRef?.current}
		>
			<div className="override-popup">
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
			</InspectorControls>
			<BlockControls group="other">
				<ToolbarGroup>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="insert"
						label="Add"
						onClick={onAdd}
						ref={toolbarButtonRef}
					/>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="update"
						label="Update"
						onClick={onReplace}
						ref={toolbarButtonRef}
					/>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="remove"
						label="Remove"
						onClick={onRemove}
						ref={toolbarButtonRef}
					/>
					<ToolbarButton
						className="toolbar-button-with-text"
						icon="trash"
						label="Trash"
						onClick={onBan}
						ref={toolbarButtonRef}
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
