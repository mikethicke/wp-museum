/**
 * WordPress dependencies
 */
import { 
	PanelBody,
	CheckboxControl,
} from '@wordpress/components';


/**
 * Inspector panel controlling whether to display title, caption for the block
 * and whether clicking on images will link to the associated object.
 * 
 * @param {object}   props                The component's properties.
 * @param {boolean}  props.displayTitle   Whether to display a title for the block.
 * @param {boolean}  props.displayCaption Whether to display a caption for the block.
 * @param {boolean}  props.displayCatID   Whether to display catalogue ID.
 * @param {boolean}  props.linkToObject   Whether images should link to objects.
 * @param {boolean}  props.initialOpen    Whether panel should be open by default.
 * @param {function} props.setAttributes  Callback function to update block attributes.
 */
const GridOptionsPanel = ( props ) => {
	const {
		displayTitle,
		displayCaption,
		displayCatID,
		linkToObject,
		initialOpen,
		setAttributes,
	} = props;

	return (
		<PanelBody
			title       = "Options"
			initialOpen = { initialOpen }
		>
			{ typeof displayTitle != 'undefined' &&
				<CheckboxControl
					label = 'Display Title'
					checked = { displayTitle }
					onChange = { ( val ) => setAttributes( { displayTitle: val } ) }
				/>
			}
			{ typeof displayCaption != 'undefined' &&
				<CheckboxControl
					label = 'Display Caption'
					checked = { displayCaption }
					onChange = { ( val ) => setAttributes( { displayCaption: val } ) }
				/>
			}
			{ typeof displayCatID != 'undefined' &&
				<CheckboxControl
					label = 'Display Catalogue ID'
					checked = { displayCatID }
					onChange = { ( val ) => setAttributes( { displayCatID: val } ) }
				/>
			}
			{ typeof linkToObject != 'undefined' &&
				<CheckboxControl
					label = 'Link to Objects'
					checked = { linkToObject }
					onChange = { ( val ) => setAttributes( { linkToObject: val } ) }
				/>
			}
		</PanelBody>
	);
}

export default GridOptionsPanel;