/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Popover, ToolbarButton, ToolbarGroup } from '@wordpress/components';
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
		postTitle,
		postUpdated,
		overrideRule
	},
	className,
	setAttributes
}) => {

	const blockProps = useBlockProps();

	const [ showPopup, setShowPopup ] = useState( false );

	const onToggle = () => {
		// Set up the anchorRange when the Popover is opened.
		setAttributes( {
			postUpdated: true,
			overrideRule: 'add'
		} );
		setShowPopup( ( showPopup ) => ! showPopup );
	};

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
			This is a popup.
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
