/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Popover, ToolbarButton, ToolbarGroup, SelectControl } from '@wordpress/components';
import { BlockControls, InnerBlocks, useBlockProps, store as blockEditorStore } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';


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
const SiteAutomationItemBlockEdit = ({
	attributes: {
		postTitle = '',
		postUpdated = false,
		overrideRule = '',
		overrideData = {}
	},
	className,
	setAttributes
}) => {

	const blockProps = useBlockProps();
	const [ showPopup, setShowPopup ] = useState( false );
	const [ postSearch, setPostSearch ] = useState( '' );

	const onToggle = () => {
		setShowPopup( ( showPopup ) => ! showPopup );
	};

	const handleOverride = () => {
		if( '' === overrideRule ) {
			alert('Please select override rule');
			return;
		}
		setAttributes( {
			overrideData: {
				'postID': 3132,
				'postTitle': 'New post title',
			},
			postUpdated: true,
		} );
	}

	const overridePopover = showPopup && (
		<Popover
			className="override-map-popover"
			position="bottom center"
			focusOnMount="firstElement"
			key="override-popover"
			expandOnMobile={ true }
			headerTitle={ __(
				'Override',
				'Override'
			) }
		>
			<div className='override-popup'>
				<div className='override-row'>
					<span>Please</span>
					<SelectControl
						label="Size"
						value={ overrideRule }
						options={ [
							{ label: 'Please select override type', value: '' },
							{ label: 'Add a post here', value: 'add' },
							{ label: 'Replace this post', value: 'replace' },
							{ label: 'Remove this post', value: 'remove' },
							{ label: 'Ban this post', value: 'ban' },
						] }
						onChange={ ( newRule ) => setAttributes( { overrideRule: newRule } ) }
						__nextHasNoMarginBottom
					/>
				</div>
				<div className='override-row'>
					<input
						type='text'
						placeholder='Post search..'
						value={postSearch}
						onChange={(event) => setPostSearch(event.target.value)}
					/>
				</div>
				<div className='override-row'>
					<label>Expire override on:</label>
					<input type='text' value='2' /> <span>hours</span>
				</div>
				<input type='submit' value='Submit Override' className='button-primary' onClick={handleOverride} />
			</div>
		</Popover>
	);

	return (
		<div className={className}>
			<BlockControls group="other">
				<ToolbarGroup>
					<ToolbarButton
						className={ `toolbar-button-with-text` }
						icon="admin-generic"
						isPressed={ showPopup }
						label={ 'Override' }
						onClick={ onToggle }
					/>
				</ToolbarGroup>
			</BlockControls>
			<div className='override-popup'>
				{ overridePopover }
			</div>
			<div { ...blockProps }>
				{postTitle}
			</div>
		</div>
	);
};
// Set the propTypes
SiteAutomationItemBlockEdit.propTypes = editPropsShape;

export default SiteAutomationItemBlockEdit;
